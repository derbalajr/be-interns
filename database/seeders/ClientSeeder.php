<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'name' => 'Shaza Essawy',
                'email' => 'shaza.essawy@example.com',
                'phone' => '+20 100 123 4567',
                'address' => 'New Cairo, Cairo',
            ],
            [
                'name' => 'Omar Khaled',
                'email' => 'omar.khaled@example.com',
                'phone' => '+20 100 765 4321',
                'address' => '6th of October, Giza',
            ],
            [
                'name' => 'Mariam Ali',
                'email' => 'mariam.ali@example.com',
                'phone' => '+20 111 222 3344',
                'address' => 'Sheikh Zayed, Giza',
            ],
            [
                'name' => 'Ahmed Hassan',
                'email' => 'ahmed.hassan@example.com',
                'phone' => '+20 122 333 4455',
                'address' => 'Maadi, Cairo',
            ],
        ];

        foreach ($clients as $client) {
            Client::updateOrCreate(
                ['email' => $client['email']],
                $client,
            );
        }
    }
}
