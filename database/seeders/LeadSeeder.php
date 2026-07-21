<?php

namespace Database\Seeders;

use App\Models\Lead;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Lead::create([
            'name' => 'Ahmed Ali',
            'email' => 'ahmed@example.com',
            'phone' => '01012345678',
            'status' => 'new',
            'agent_id' => null,
        ]);

        Lead::create([
            'name' => 'Sara Mohamed',
            'email' => 'sara@example.com',
            'phone' => '01087654321',
            'status' => 'contacted',
            'agent_id' => null,
        ]);

        Lead::create([
            'name' => 'Omar Hassan',
            'email' => 'omar@example.com',
            'phone' => '01111111111',
            'status' => 'qualified',
            'agent_id' => null,
        ]);
    }
}
