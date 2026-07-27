<?php

namespace Database\Seeders;

use App\Enums\LeadStage;
use App\Models\Lead;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Lead::updateOrCreate(
            [
                'email' => 'ahmed@example.com',
            ],
            [
                'name' => 'Ahmed Ali',
                'phone' => '01012345678',
                'source' => 'website',
                'stage' => LeadStage::New,
                'agent_id' => null,
            ]
        );

        Lead::updateOrCreate(
            [
                'email' => 'sara@example.com',
            ],
            [
                'name' => 'Sara Mohamed',
                'phone' => '01087654321',
                'source' => 'referral',
                'stage' => LeadStage::Contacted,
                'agent_id' => null,
            ]
        );

        Lead::updateOrCreate(
            [
                'email' => 'omar@example.com',
            ],
            [
                'name' => 'Omar Hassan',
                'phone' => '01111111111',
                'source' => 'phone_call',
                'stage' => LeadStage::Qualified,
                'agent_id' => null,
            ]
        );
    }
}
