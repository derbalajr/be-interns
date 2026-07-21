<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company() . ' Compound';

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'location' => fake()->city(),
            'description' => fake()->paragraph(),
        ];
    }
}

