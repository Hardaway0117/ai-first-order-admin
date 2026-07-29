<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                '無線藍牙耳機', '機械式鍵盤', '電競滑鼠', '27吋 4K 螢幕',
                '筆電散熱支架', 'Type-C 擴充座', '行動電源 20000mAh', '智慧手環',
                '網路攝影機', '降噪耳罩式耳機', '無線充電盤', '藍牙喇叭',
            ]),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-??###')),
            'price' => fake()->randomFloat(2, 100, 20000),
            'stock' => fake()->numberBetween(0, 500),
            'is_active' => fake()->boolean(90),
        ];
    }
}
