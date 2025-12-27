<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition()
    {
        return [
            'restaurant_id' => null,
            'name' => $this->faker->city . ' Branch',
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'postal_code' => $this->faker->postcode,
            'country' => $this->faker->countryCode,
            'coordinates' => ['lat' => $this->faker->latitude, 'lng' => $this->faker->longitude],
            'meta' => [],
        ];
    }
}
