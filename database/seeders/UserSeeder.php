<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = 'password123';

        $users = [
            [
                'name' => 'Yasmine',
                'email' => 'yasmine.46y@gmail.com',
                'role' => 'super-admin',
            ],
            [
                'name' => 'Lomy',
                'email' => 'lomy18106@gmail.com',
                'role' => 'super-admin',
            ],
            [
                'name' => 'Daniel',
                'email' => 'Danielnagy589@gmail.com',
                'role' => 'super-admin',
            ],
            [
                'name' => 'Salma',
                'email' => 'salma.waled@addressinv.com',
                'role' => 'super-admin',
            ],
            [
                'name' => 'Gamila',
                'email' => 'gamila.mamdouh@addressinv.com',
                'role' => 'super-admin',
            ],
            [
                'name' => 'Lala',
                'email' => 'lala@lala.lala',
                'role' => 'super-admin',
            ],

            // Test Manager
            [
                'name' => 'Test Manager',
                'email' => 'manager@test.com',
                'role' => 'manager',
            ],

            // Test Agent
            [
                'name' => 'Test Agent',
                'email' => 'agent@test.com',
                'role' => 'agent',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $password,
                ]
            );

            $user->syncRoles([$data['role']]);
        }
    }
}
