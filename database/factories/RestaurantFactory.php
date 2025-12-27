<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'slug' => Str::slug($this->faker->company) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'description' => $this->faker->optional()->sentence,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->companyEmail,
            'status' => 'active',
            'settings' => [],
            'meta' => [],
        ];
    }
}
