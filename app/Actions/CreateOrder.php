<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateOrder
{
    /**
     * 以交易建立訂單：行級鎖檢查庫存、扣庫存、快照商品名稱與單價、計算總額。
     *
     * @param  array{customer_id: int, notes?: ?string, items: array<int, array{product_id: int, quantity: int}>}  $data
     */
    public function handle(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'customer_id' => $data['customer_id'],
                'status' => OrderStatus::Pending,
                'notes' => $data['notes'] ?? null,
            ]);

            $total = 0;

            foreach ($data['items'] as $line) {
                $product = Product::query()->lockForUpdate()->findOrFail($line['product_id']);

                if ($product->stock < $line['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => __('Insufficient stock for :product.', ['product' => $product->name]),
                    ]);
                }

                $product->decrement('stock', $line['quantity']);

                $subtotal = round($product->price * $line['quantity'], 2);
                $total += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $line['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total' => $total]);

            return $order;
        });
    }
}
