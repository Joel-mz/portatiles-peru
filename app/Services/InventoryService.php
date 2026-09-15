<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InventoryService
{
    /**
     * Confirms an order's payment and deducts stock in an atomic transaction.
     * IDEMPOTENT: If already paid, it will not deduct stock twice.
     *
     * @throws \Exception
     */
    public function confirmOrderPayment(Order $order, ?User $user = null): bool
    {
        return DB::transaction(function () use ($order, $user) {
            // Reload with lock for update to prevent concurrent double-confirmations
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if (! $lockedOrder) {
                throw new InvalidArgumentException('El pedido especificado no existe.');
            }

            // IDEMPOTENCY CHECK
            if ($lockedOrder->payment_status === 'paid') {
                return true;
            }

            // Verify and deduct stock for all items
            foreach ($lockedOrder->items as $item) {
                $product = Product::where('id', $item->product_id)->lockForUpdate()->first();

                if (! $product) {
                    continue;
                }

                $stockBefore = $product->stock;
                $deductQuantity = (int) $item->quantity;

                // Deduct stock (ensure it doesn't go below zero unless intended)
                $newStock = max(0, $stockBefore - $deductQuantity);
                $product->update(['stock' => $newStock]);

                // Record in Stock Movements Kardex
                StockMovement::create([
                    'product_id' => $product->id,
                    'order_id' => $lockedOrder->id,
                    'type' => 'sale',
                    'quantity' => -$deductQuantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $newStock,
                    'reason' => "Venta confirmada Pedido #{$lockedOrder->order_number}",
                    'user_id' => $user?->id,
                ]);
            }

            // Update order payment status to paid
            $lockedOrder->update([
                'payment_status' => 'paid',
                'order_status' => 'processing',
                'paid_at' => now(),
                'verified_by_user_id' => $user?->id,
                'rejection_reason' => null,
            ]);

            return true;
        });
    }

    /**
     * Rejects an order payment evidence. Stock remains untouched.
     */
    public function rejectOrderPayment(Order $order, string $reason, ?User $user = null): bool
    {
        return $order->update([
            'payment_status' => 'rejected',
            'order_status' => 'cancelled',
            'rejection_reason' => $reason,
            'verified_by_user_id' => $user?->id,
        ]);
    }

    /**
     * Manually adjusts product stock and logs movement.
     */
    public function adjustStock(Product $product, int $newStock, string $reason, ?User $user = null): StockMovement
    {
        return DB::transaction(function () use ($product, $newStock, $reason, $user) {
            $lockedProduct = Product::where('id', $product->id)->lockForUpdate()->first();
            $stockBefore = $lockedProduct->stock;
            $diff = $newStock - $stockBefore;
            $type = $diff >= 0 ? 'entry' : 'adjustment';

            $lockedProduct->update(['stock' => $newStock]);

            return StockMovement::create([
                'product_id' => $lockedProduct->id,
                'order_id' => null,
                'type' => $type,
                'quantity' => $diff,
                'stock_before' => $stockBefore,
                'stock_after' => $newStock,
                'reason' => $reason,
                'user_id' => $user?->id,
            ]);
        });
    }
}
