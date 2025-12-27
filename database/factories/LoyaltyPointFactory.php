<?php

namespace Database\Factories;

use App\Models\LoyaltyPoint;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoyaltyPointFactory extends Factory
{
    protected $model = LoyaltyPoint::class;

    public function definition()
    {
        return [
            'user_id' => null,
            'points' => $this->faker->numberBetween(1, 500),
            'reason' => $this->faker->sentence,
            'meta' => [],
        ];
    }
}
