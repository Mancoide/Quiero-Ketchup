<?php

namespace Database\Factories;

use App\Models\Assignment;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition()
    {
        return [
            'order_id' => null,
            'user_id' => null,
            'status' => 'assigned',
            'meta' => [],
        ];
    }
}
