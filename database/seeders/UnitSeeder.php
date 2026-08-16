<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Unit;
use App\Models\UnitMedia;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Real property photos (Unsplash) keyed by unit type. Stored as absolute
     * URLs in unit_media; UnitResource serves them as-is (see mediaUrl()).
     *
     * @var array<string, list<string>>
     */
    private const IMAGES = [
        'Apartment' => [
            'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&q=80',
            'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?w=800&q=80',
            'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?w=800&q=80',
        ],
        'Townhouse' => [
            'https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=800&q=80',
            'https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=800&q=80',
        ],
        'Villa' => [
            'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800&q=80',
            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&q=80',
            'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800&q=80',
            'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?w=800&q=80',
        ],
    ];

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
            foreach ($blueprint as $index => $unit) {
                $model = Unit::updateOrCreate(
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

                $this->seedPhoto($model, $unit['type'], $index);
            }
        }
    }

    /**
     * Give the unit a cover photo via the unit_media table (idempotent).
     */
    private function seedPhoto(Unit $unit, string $type, int $index): void
    {
        $pool = self::IMAGES[$type] ?? self::IMAGES['Apartment'];
        $url = $pool[$index % count($pool)];

        UnitMedia::updateOrCreate(
            [
                'unit_id' => $unit->id,
                'path' => $url,
            ],
            [
                'type' => UnitMedia::TYPE_PHOTO,
            ],
        );
    }
}
