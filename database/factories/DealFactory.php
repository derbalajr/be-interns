<?php

namespace Database\Factories;

use App\Models\Deal;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DealFactory extends Factory
{
    protected $model = Deal::class;

    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'unit_id' => null,
            'agent_id' => User::factory(),
            'stage' => 'new',
            'value' => $this->faker->randomFloat(2, 50000, 500000),
            'expected_close' => $this->faker->dateTimeBetween('now', '+3 months'),
        ];
    }
}
