<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('U-###??'),
            'type' => fake()->randomElement([
                'apartment',
                'villa',
                'studio',
                'duplex',
            ]),
            'area' => fake()->randomFloat(2, 50, 500),
            'price' => fake()->randomFloat(2, 500000, 10000000),
            'status' => 'available',
            'project_id' => Project::factory(),
        ];
    }
}
