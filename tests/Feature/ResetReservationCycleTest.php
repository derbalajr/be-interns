<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Handover;
use App\Models\Project;
use App\Models\Reservation;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResetReservationCycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_clears_reservations_and_handovers_and_frees_units(): void
    {
        $agent = User::factory()->create(['tenant' => 'marq']);
        $client = Client::create(['name' => 'Buyer', 'email' => 'buyer@example.com']);

        $unit = Unit::factory()->create([
            'project_id' => Project::factory(),
            'status' => Unit::STATUS_SOLD,
        ]);

        $reservation = Reservation::create([
            'unit_id' => $unit->id,
            'client_id' => $client->id,
            'agent_id' => $agent->id,
            'status' => 'confirmed',
            'reserved_price' => $unit->price,
            'reserved_at' => now(),
        ]);

        Handover::create([
            'unit_id' => $unit->id,
            'client_id' => $client->id,
            'handover_date' => now()->addDay()->toDateString(),
            'status' => 'scheduled',
        ]);

        $this->artisan('reservations:reset-cycle --force')->assertSuccessful();

        $this->assertDatabaseCount('reservations', 0);
        $this->assertDatabaseCount('handovers', 0);
        $this->assertDatabaseHas('units', [
            'id' => $unit->id,
            'status' => Unit::STATUS_AVAILABLE,
        ]);

        // Clients are untouched.
        $this->assertDatabaseHas('clients', ['id' => $client->id]);
    }
}
