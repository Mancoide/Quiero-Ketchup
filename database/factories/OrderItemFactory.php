<?php

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        $qty = $this->faker->numberBetween(1,4);
        $unit = $this->faker->randomFloat(2, 1, 50);
        return [
            'order_id' => null,
            'product_id' => null,
            'quantity' => $qty,
            'unit_price' => $unit,
            'total_price' => $qty * $unit,
            'meta' => [],
        ];
    }
}
