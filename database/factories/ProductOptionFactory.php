<?php

namespace Database\Factories;

use App\Models\ProductOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductOptionFactory extends Factory
{
    protected $model = ProductOption::class;

    public function definition()
    {
        return [
            'product_id' => null,
            'name' => $this->faker->word,
            'type' => 'select',
            'required' => false,
            'meta' => [],
        ];
    }
}
