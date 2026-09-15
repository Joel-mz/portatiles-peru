<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Creates an order from validated data and cart items.
     * CRITICAL RULE: This NEVER deducts stock! Stock is only deducted when confirmed 'paid'.
     */
    public function createOrder(array $data, array $cartItems, ?UploadedFile $proofFile = null): Order
    {
        return DB::transaction(function () use ($data, $cartItems, $proofFile) {
            // Generate unique human-readable order number
            $year = date('Y');
            $random = strtoupper(Str::random(5));
            $count = Order::count() + 1;
            $orderNumber = sprintf('PED-%s-%05d', $year, $count);

            // Fetch products from DB to strictly compute prices on the server
            $productIds = array_column($cartItems, 'id');
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $subtotal = 0.0;
            $itemsToCreate = [];

            foreach ($cartItems as $item) {
                $productId = $item['id'];
                $quantity = max(1, (int) ($item['quantity'] ?? 1));

                if (! isset($products[$productId])) {
                    continue;
                }

                $product = $products[$productId];
                $unitPrice = (float) $product->effective_price;
                $lineSubtotal = round($unitPrice * $quantity, 2);

                $subtotal += $lineSubtotal;

                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $unitPrice,
                    'quantity' => $quantity,
                    'subtotal' => $lineSubtotal,
                ];
            }

            // Coupon discount calculation
            $discount = 0.0;
            if (! empty($data['coupon_code'])) {
                $coupon = Coupon::where('code', strtoupper(trim($data['coupon_code'])))->first();
                if ($coupon && $coupon->isValid($subtotal)) {
                    $discount = $coupon->calculateDiscount($subtotal);
                    $coupon->increment('used_count');
                }
            }

            $totalAfterDiscount = max(0, $subtotal - $discount);
            // In Peru, commercial prices usually include IGV (18%). We calculate the breakdown:
            $igv = round($totalAfterDiscount * 0.18 / 1.18, 2);
            $total = $totalAfterDiscount;

            // Handle payment proof upload
            $proofPath = null;
            $paymentStatus = 'pending';

            if ($proofFile && $proofFile->isValid()) {
                $filename = 'proof_'.time().'_'.Str::random(10).'.'.$proofFile->getClientOriginalExtension();
                $proofPath = $proofFile->storeAs('proofs', $filename, 'public');
                // Proof uploaded means pending human review, NOT paid!
                $paymentStatus = 'pending_review';
            }

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'customer_document_type' => $data['customer_document_type'] ?? 'DNI',
                'customer_document_number' => $data['customer_document_number'] ?? null,
                'department' => $data['department'] ?? 'San Martín',
                'province' => $data['province'] ?? null,
                'district' => $data['district'] ?? null,
                'address' => $data['address'],
                'reference' => $data['reference'] ?? null,
                'shipping_type' => $data['shipping_type'] ?? 'delivery',
                'notes' => $data['notes'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'igv' => $igv,
                'total' => $total,
                'payment_method' => $data['payment_method'] ?? 'yape',
                'payment_status' => $paymentStatus,
                'order_status' => 'pending',
                'payment_proof' => $proofPath ? '/storage/'.$proofPath : null,
                'proof_uploaded_at' => $proofPath ? now() : null,
            ]);

            foreach ($itemsToCreate as $itemData) {
                $order->items()->create($itemData);
            }

            return $order;
        });
    }

    /**
     * Attaches proof of payment after order creation
     */
    public function attachProof(Order $order, UploadedFile $proofFile): Order
    {
        $filename = 'proof_'.time().'_'.Str::random(10).'.'.$proofFile->getClientOriginalExtension();
        $proofPath = $proofFile->storeAs('proofs', $filename, 'public');

        $order->update([
            'payment_proof' => '/storage/'.$proofPath,
            'proof_uploaded_at' => now(),
            // Ensure status is marked as pending_review, NOT paid
            'payment_status' => 'pending_review',
        ]);

        return $order;
    }
}
