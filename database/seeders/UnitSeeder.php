<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();

        foreach ($projects as $project) {

            Unit::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'code' => 'A101',
                ],
                [
                    'type' => 'Apartment',
                    'area' => 120,
                    'price' => 4500000,
                    'status' => Unit::STATUS_AVAILABLE,
                ]
            );

            Unit::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'code' => 'A102',
                ],
                [
                    'type' => 'Apartment',
                    'area' => 140,
                    'price' => 5200000,
                    'status' => Unit::STATUS_AVAILABLE,
                ]
            );

            Unit::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'code' => 'V201',
                ],
                [
                    'type' => 'Villa',
                    'area' => 310,
                    'price' => 12800000,
                    'status' => Unit::STATUS_AVAILABLE,
                ]
            );
        }
    }
}