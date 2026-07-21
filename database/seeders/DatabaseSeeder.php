<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $manager = User::factory()->create([
            'name' => 'Demo Manager',
            'email' => 'manager@crm.test',
            'password' => 'password',
        ]);

        $agents = User::factory()
            ->count(5)
            ->sequence(
                ['name' => 'Mariam Hassan'],
                ['name' => 'Ahmed Ali'],
                ['name' => 'Sara Mohamed'],
                ['name' => 'Omar Khaled'],
                ['name' => 'Nour Adel'],
            )
            ->create();

        Lead::factory()
            ->count(30)
            ->recycle($agents)
            ->create();
    }
}