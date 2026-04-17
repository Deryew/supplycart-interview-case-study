<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => Order::generateOrderNumber(),
            'total_amount' => fake()->numberBetween(1000, 100000),
            'status' => 'pending',
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
