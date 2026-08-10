<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'slug' => 'palm-hills',
                'name' => 'Palm Hills',
                'location' => 'New Cairo',
                'description' => 'Gated community with landscaped gardens and family villas.',
            ],
            [
                'slug' => 'mountain-view',
                'name' => 'Mountain View',
                'location' => '6th of October',
                'description' => 'Modern apartments and townhouses close to the city.',
            ],
            [
                'slug' => 'marassi',
                'name' => 'Marassi',
                'location' => 'North Coast',
                'description' => 'Beachfront standalone villas and chalets on the North Coast.',
            ],
            [
                'slug' => 'o-west',
                'name' => 'O West',
                'location' => '6th of October',
                'description' => 'Premium residential compound with apartments and villas.',
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(
                ['slug' => $project['slug']],
                $project,
            );
        }
    }
}
