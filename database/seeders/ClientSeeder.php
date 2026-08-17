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
                'national_id' => '29508121201928',
                'full_arabic_name' => 'شذى العيسوي محمد',
                'marital_status' => 'single',
                'job' => 'مهندسة معمارية',
                'birthdate' => '1995-08-12',
                'expiry_date' => '2030-08-11',
            ],
            [
                'name' => 'Omar Khaled',
                'email' => 'omar.khaled@example.com',
                'phone' => '+20 100 765 4321',
                'address' => '6th of October, Giza',
                'national_id' => '29003155600341',
                'full_arabic_name' => 'عمر خالد إبراهيم',
                'marital_status' => 'married',
                'job' => 'محاسب',
                'birthdate' => '1990-03-15',
                'expiry_date' => '2029-03-14',
            ],
            [
                'name' => 'Mariam Ali',
                'email' => 'mariam.ali@example.com',
                'phone' => '+20 111 222 3344',
                'address' => 'Sheikh Zayed, Giza',
                'national_id' => '29211203400876',
                'full_arabic_name' => 'مريم علي حسن',
                'marital_status' => 'single',
                'job' => 'طبيبة',
                'birthdate' => '1992-11-20',
                'expiry_date' => '2031-11-19',
            ],
            [
                'name' => 'Ahmed Hassan',
                'email' => 'ahmed.hassan@example.com',
                'phone' => '+20 122 333 4455',
                'address' => 'Maadi, Cairo',
                'national_id' => '28806012700155',
                'full_arabic_name' => 'أحمد حسن عبد الله',
                'marital_status' => 'married',
                'job' => 'رجل أعمال',
                'birthdate' => '1988-06-01',
                'expiry_date' => '2028-05-31',
            ],
            [
                'name' => 'Nour Adel',
                'email' => 'nour.adel@example.com',
                'phone' => '+20 106 555 7788',
                'address' => 'Nasr City, Cairo',
                'national_id' => '29704186100522',
                'full_arabic_name' => 'نور عادل السيد',
                'marital_status' => 'single',
                'job' => 'مسوقة',
                'birthdate' => '1997-04-18',
                'expiry_date' => '2032-04-17',
            ],
            [
                'name' => 'Youssef Ibrahim',
                'email' => 'youssef.ibrahim@example.com',
                'phone' => '+20 128 999 1122',
                'address' => 'Heliopolis, Cairo',
                'national_id' => '28509233300419',
                'full_arabic_name' => 'يوسف إبراهيم كامل',
                'marital_status' => 'married',
                'job' => 'مدير مبيعات',
                'birthdate' => '1985-09-23',
                'expiry_date' => '2027-09-22',
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
