<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 示範帳號（密碼為 factory 預設 "password"，僅供 demo，正式環境請移除）
        User::factory()->create([
            'name' => '示範管理員',
            'email' => 'admin@example.com',
        ]);

        User::factory()->staff()->create([
            'name' => '示範員工',
            'email' => 'staff@example.com',
        ]);

        $products = Product::factory(20)->create();

        Customer::factory(12)->create()->each(function (Customer $customer) use ($products) {
            Order::factory(fake()->numberBetween(1, 4))
                ->for($customer)
                ->create()
                ->each(function (Order $order) use ($products) {
                    $items = $products->random(fake()->numberBetween(1, 4))
                        ->map(function (Product $product) {
                            $quantity = fake()->numberBetween(1, 5);

                            return [
                                'product_id' => $product->id,
                                'product_name' => $product->name,
                                'quantity' => $quantity,
                                'unit_price' => $product->price,
                                'subtotal' => round($quantity * $product->price, 2),
                            ];
                        });

                    $order->items()->createMany($items->all());
                    $order->update(['total' => $items->sum('subtotal')]);
                });
        });
    }
}
