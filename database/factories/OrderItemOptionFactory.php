<?php

namespace Database\Factories;

use App\Models\OrderItemOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemOptionFactory extends Factory
{
    protected $model = OrderItemOption::class;

    public function definition()
    {
        return [
            'order_item_id' => null,
            'product_option_item_id' => null,
            'name' => $this->faker->word,
            'price' => $this->faker->randomFloat(2, 0, 10),
            'meta' => [],
        ];
    }
}
