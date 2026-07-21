<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            LeadSeeder::class,
        ]);

        $manager = User::factory()->create([
            'name' => 'Demo Manager',
            'email' => 'manager@crm.test',
            'password' => 'password',
        ]);

        $manager->assignRole('manager');

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

        foreach ($agents as $agent) {
            $agent->assignRole('agent');
        }

        Lead::factory()
            ->count(30)
            ->recycle($agents)
            ->create();
    }
}
