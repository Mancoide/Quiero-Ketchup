<?php

namespace Database\Factories;

use App\Models\ProductOptionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductOptionItemFactory extends Factory
{
    protected $model = ProductOptionItem::class;

    public function definition()
    {
        return [
            'product_option_id' => null,
            'name' => $this->faker->word,
            'price' => $this->faker->randomFloat(2, 0, 20),
            'meta' => [],
        ];
    }
}
