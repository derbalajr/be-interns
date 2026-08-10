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

        // A varied catalogue per project: apartments, townhouses and villas
        // across the three statuses and a range of prices/areas.
        $blueprint = [
            ['code' => 'A101', 'type' => 'Apartment', 'area' => 120, 'price' => 4500000, 'status' => Unit::STATUS_AVAILABLE],
            ['code' => 'A102', 'type' => 'Apartment', 'area' => 140, 'price' => 5200000, 'status' => Unit::STATUS_AVAILABLE],
            ['code' => 'A103', 'type' => 'Apartment', 'area' => 165, 'price' => 6100000, 'status' => Unit::STATUS_RESERVED],
            ['code' => 'T110', 'type' => 'Townhouse', 'area' => 210, 'price' => 8600000, 'status' => Unit::STATUS_AVAILABLE],
            ['code' => 'T111', 'type' => 'Townhouse', 'area' => 235, 'price' => 9400000, 'status' => Unit::STATUS_SOLD],
            ['code' => 'V201', 'type' => 'Villa', 'area' => 310, 'price' => 12800000, 'status' => Unit::STATUS_AVAILABLE],
            ['code' => 'V204', 'type' => 'Villa', 'area' => 320, 'price' => 14200000, 'status' => Unit::STATUS_AVAILABLE],
            ['code' => 'V208', 'type' => 'Villa', 'area' => 360, 'price' => 16750000, 'status' => Unit::STATUS_RESERVED],
        ];

        foreach ($projects as $project) {
            foreach ($blueprint as $unit) {
                Unit::updateOrCreate(
                    [
                        'project_id' => $project->id,
                        'code' => $unit['code'],
                    ],
                    [
                        'type' => $unit['type'],
                        'area' => $unit['area'],
                        'price' => $unit['price'],
                        'status' => $unit['status'],
                    ],
                );
            }
        }
    }
}
