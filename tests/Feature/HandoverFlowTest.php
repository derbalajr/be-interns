<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Handover;
use App\Models\Project;
use App\Models\Reservation;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HandoverFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function manager(): User
    {
        $manager = User::factory()->create(['tenant' => 'marq']);
        $manager->assignRole('manager');

        return $manager;
    }

    private function client(string $email = 'buyer@example.com'): Client
    {
        return Client::create(['name' => 'Buyer One', 'email' => $email]);
    }

    private function unit(string $status): Unit
    {
        return Unit::factory()->create([
            'project_id' => Project::factory(),
            'status' => $status,
        ]);
    }

    private function reservation(Unit $unit, Client $client, User $agent, string $status): Reservation
    {
        return Reservation::create([
            'unit_id' => $unit->id,
            'client_id' => $client->id,
            'agent_id' => $agent->id,
            'status' => $status,
            'reserved_price' => $unit->price,
            'reserved_at' => now(),
        ]);
    }

    public function test_selling_a_unit_confirms_its_reservation(): void
    {
        $manager = $this->manager();
        $unit = $this->unit(Unit::STATUS_RESERVED);
        $reservation = $this->reservation($unit, $this->client(), $manager, 'pending');

        $this->actingAs($manager, 'api')
            ->patchJson("/api/units/{$unit->id}/sell")
            ->assertOk();

        $this->assertDatabaseHas('units', ['id' => $unit->id, 'status' => Unit::STATUS_SOLD]);
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id, 'status' => 'confirmed']);
    }

    public function test_handover_cannot_be_scheduled_for_a_unit_that_is_not_sold(): void
    {
        $manager = $this->manager();
        $unit = $this->unit(Unit::STATUS_RESERVED);
        $this->reservation($unit, $this->client(), $manager, 'pending');

        $this->actingAs($manager, 'api')
            ->postJson('/api/handovers', [
                'unit_id' => $unit->id,
                'handover_date' => now()->addDay()->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Unit must be sold to schedule handover');

        $this->assertDatabaseCount('handovers', 0);
    }

    public function test_handover_client_is_derived_from_the_reservation_not_the_request(): void
    {
        $manager = $this->manager();
        $unit = $this->unit(Unit::STATUS_SOLD);
        $buyer = $this->client('actual-buyer@example.com');
        $reservation = $this->reservation($unit, $buyer, $manager, 'confirmed');

        // A different client is sent in the request and must be ignored.
        $someoneElse = $this->client('someone-else@example.com');

        $this->actingAs($manager, 'api')
            ->postJson('/api/handovers', [
                'unit_id' => $unit->id,
                'client_id' => $someoneElse->id,
                'handover_date' => now()->addDay()->toDateString(),
            ])
            ->assertStatus(201);

        $this->assertDatabaseHas('handovers', [
            'unit_id' => $unit->id,
            'reservation_id' => $reservation->id,
            'client_id' => $buyer->id,
        ]);
    }

    public function test_completing_a_handover_completes_the_reservation(): void
    {
        $manager = $this->manager();
        $unit = $this->unit(Unit::STATUS_SOLD);
        $reservation = $this->reservation($unit, $this->client(), $manager, 'confirmed');

        $handover = Handover::create([
            'unit_id' => $unit->id,
            'reservation_id' => $reservation->id,
            'client_id' => $reservation->client_id,
            'handover_date' => now()->addDay()->toDateString(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($manager, 'api')
            ->postJson("/api/handovers/{$handover->id}/complete")
            ->assertOk();

        $this->assertDatabaseHas('handovers', ['id' => $handover->id, 'status' => 'completed']);
        $this->assertDatabaseHas('reservations', ['id' => $reservation->id, 'status' => 'completed']);
    }

    public function test_a_unit_can_only_have_one_active_handover(): void
    {
        $manager = $this->manager();
        $unit = $this->unit(Unit::STATUS_SOLD);
        $this->reservation($unit, $this->client(), $manager, 'confirmed');

        $payload = [
            'unit_id' => $unit->id,
            'handover_date' => now()->addDay()->toDateString(),
        ];

        $this->actingAs($manager, 'api')->postJson('/api/handovers', $payload)->assertStatus(201);
        $this->actingAs($manager, 'api')->postJson('/api/handovers', $payload)
            ->assertStatus(422)
            ->assertJsonPath('message', 'This unit already has a handover.');

        $this->assertDatabaseCount('handovers', 1);
    }
}
