<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'status' => fake()->randomElement(OrderStatus::cases()),
            'total' => 0,
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
            'created_at' => fake()->dateTimeBetween('-60 days'),
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => OrderStatus::Pending]);
    }
}
