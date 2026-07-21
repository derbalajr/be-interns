<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@crm.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // 2. Create Manager
        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@crm.com',
            'password' => Hash::make('password'),
        ]);
        $manager->assignRole('manager');

        // 3. Create Agent
        $agent = User::factory()->create([
            'name' => 'Agent User',
            'email' => 'agent@crm.com',
            'password' => Hash::make('password'),
        ]);
        $agent->assignRole('agent');
    }
}