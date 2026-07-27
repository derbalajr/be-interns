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
        // 1. Create Admin
       $password = 'password123';

        $users = [
            [
                'name' => 'Yasmine',
                'email' => 'yasmine.46y@gmail.com',
                'role' => 'super-admin',
                'tenant' => 'marq',
            ],
            [
                'name' => 'Lomy',
                'email' => 'lomy18106@gmail.com',
                'role' => 'super-admin',
                'tenant' => 'marq',
            ],
            [
                'name' => 'Daniel',
                'email' => 'Danielnagy589@gmail.com',
                'role' => 'super-admin',
                'tenant' => 'marq',
            ],
            [
                'name' => 'Salma',
                'email' => 'salma.waled@addressinv.com',
                'role' => 'super-admin',
                'tenant' => 'tai',
            ],
            [
                'name' => 'Gamila',
                'email' => 'gamila.mamdouh@addressinv.com',
                'role' => 'super-admin',
                'tenant' => 'tai',
            ],
            [
                'name' => 'Lala',
                'email' => 'lala@lala.lala',
                'role' => 'super-admin',
                'tenant' => 'marq',
            ],
            [
                'name' => 'Derbala M',
                'email' => 'derbala@marq.com',
                'role' => 'super-admin',
                'tenant' => 'marq',
            ],
            [
                'name' => 'Derbala T',
                'email' => 'derbala@tai.com',
                'role' => 'super-admin',
                'tenant' => 'tai',
            ],

            // Test Manager
            [
                'name' => 'Manager M',
                'email' => 'manager@marq.com',
                'role' => 'manager',
                'tenant' => 'marq',
            ],
[
                'name' => 'Manager T',
                'email' => 'manager@tai.com',
                'role' => 'manager',
                'tenant' => 'tai',
            ],

            // Test Agent
            [
                'name' => 'Agent M',
                'email' => 'agent@marq.com',
                'role' => 'agent',
                'tenant' => 'marq',
            ],
            [
                'name' => 'Agent T',
                'email' => 'agent@tai.com',
                'role' => 'agent',
                'tenant' => 'tai',
            ],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $password,
                    'tenant' => $data['tenant'],
                ]
            );

            $user->forceFill(['tenant' => $data['tenant']]);
            $user->save();

            $user->syncRoles([$data['role']]);
        }}
}
