<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Quote;
use Illuminate\Support\Facades\DB;

class QuoteService
{
    /**
     * Create formal quotation
     */
    public function createQuote(array $data, array $items): Quote
    {
        return DB::transaction(function () use ($data, $items) {
            $count = Quote::count() + 1;
            $quoteNumber = sprintf('COT-%06d', $count);

            $subtotal = 0.0;
            $itemsToCreate = [];

            $productIds = array_filter(array_column($items, 'product_id'));
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($items as $item) {
                $qty = max(1, (int) ($item['quantity'] ?? 1));
                $productId = $item['product_id'] ?? null;
                $product = $productId ? ($products[$productId] ?? null) : null;

                $name = $product ? $product->name : ($item['product_name'] ?? 'Producto Tecnológico');
                $sku = $product ? $product->sku : ($item['product_sku'] ?? null);
                $unitPrice = $product ? (float) $product->effective_price : (float) ($item['unit_price'] ?? 0);
                $lineSubtotal = round($unitPrice * $qty, 2);

                $subtotal += $lineSubtotal;

                $itemsToCreate[] = [
                    'product_id' => $product?->id,
                    'product_name' => $name,
                    'product_sku' => $sku,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $quote = Quote::create([
                'quote_number' => $quoteNumber,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'company_name' => $data['company_name'] ?? null,
                'company_ruc' => $data['company_ruc'] ?? null,
                'message' => $data['message'] ?? null,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'valid_until' => now()->addDays(15),
            ]);

            foreach ($itemsToCreate as $itemData) {
                $quote->items()->create($itemData);
            }

            return $quote;
        });
    }

    /**
     * Update quotation status
     */
    public function updateStatus(Quote $quote, string $status, ?string $notes = null): Quote
    {
        $quote->update([
            'status' => $status,
            'admin_notes' => $notes ?: $quote->admin_notes,
        ]);

        return $quote;
    }
}
