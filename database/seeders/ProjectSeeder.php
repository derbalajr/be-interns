<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        Project::updateOrCreate(
            ['slug' => 'palm-hills'],
            [
                'name' => 'Palm Hills',
                'location' => 'New Cairo',
                'description' => 'Demo project',
            ]
        );

        Project::updateOrCreate(
            ['slug' => 'mountain-view'],
            [
                'name' => 'Mountain View',
                'location' => '6th October',
                'description' => 'Demo project',
            ]
        );
    }
}