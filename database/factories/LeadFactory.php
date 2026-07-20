<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'source' => fake()->randomElement([
                'website',
                'referral',
                'social_media',
                'phone_call',
                'walk_in',
            ]),
            'stage' => fake()->randomElement([
                'new',
                'contacted',
                'qualified',
                'negotiation',
                'won',
                'lost',
            ]),
            'budget' => fake()->randomFloat(2, 100000, 5000000),
            'agent_id' => User::factory(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'stage' => 'lost',
        ]);
    }
}
