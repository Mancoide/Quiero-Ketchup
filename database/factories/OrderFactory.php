<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'user_id' => null,
            'restaurant_id' => null,
            'status' => OrderStatus::PENDING->value,
            'total_amount' => $this->faker->randomFloat(2, 5, 200),
            'currency' => 'PYG',
            'metadata' => [],
        ];
    }
}
