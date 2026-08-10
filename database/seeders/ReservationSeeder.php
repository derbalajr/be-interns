<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Reservation;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Gives every unit the UnitSeeder marked as "reserved" a matching
     * reservation row, so the reservations list has real, consistent data
     * (unit.status === reserved <-> a pending reservation exists).
     */
    public function run(): void
    {
        $agent = User::where('tenant', 'marq')->first();
        $clients = Client::all();

        if (!$agent || $clients->isEmpty()) {
            return;
        }

        $reservedUnits = Unit::where('status', Unit::STATUS_RESERVED)->get();

        foreach ($reservedUnits as $index => $unit) {
            $client = $clients[$index % $clients->count()];

            Reservation::updateOrCreate(
                ['unit_id' => $unit->id],
                [
                    'client_id' => $client->id,
                    'agent_id' => $agent->id,
                    'status' => 'pending',
                    'reserved_price' => $unit->price,
                    'reserved_at' => now(),
                ],
            );
        }
    }
}
