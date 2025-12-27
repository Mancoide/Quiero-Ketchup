<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition()
    {
        return [
            'user_id' => null,
            'restaurant_id' => null,
            'order_id' => null,
            'rating' => $this->faker->numberBetween(1,5),
            'comment' => $this->faker->optional()->sentence,
        ];
    }
}
