<?php

namespace Database\Factories;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    public function definition()
    {
        $start = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $end = (clone $start)->modify('+7 days');
        return [
            'code' => strtoupper($this->faker->lexify('PROMO??')),
            'type' => 'percentage',
            'value' => $this->faker->randomFloat(2, 5, 30),
            'starts_at' => $start,
            'ends_at' => $end,
            'meta' => [],
        ];
    }
}
