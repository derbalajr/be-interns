<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Demo Manager',
            'email' => 'manager@crm.test',
            'password' => 'password',
        ]);

        User::factory()
            ->count(5)
            ->sequence(
                ['name' => 'Mariam Hassan'],
                ['name' => 'Ahmed Ali'],
                ['name' => 'Sara Mohamed'],
                ['name' => 'Omar Khaled'],
                ['name' => 'Nour Adel'],
            )
            ->create();
    }
}
