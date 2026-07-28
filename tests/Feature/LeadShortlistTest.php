<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadShortlistTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_manager_can_add_unit_to_lead_shortlist(): void
    {
        $manager = User::factory()->create([
            'tenant' => 'tai',
        ]);

        $manager->assignRole('manager');

        $lead = Lead::factory()->create();

        $project = Project::factory()->create();

        $unit = Unit::factory()->create([
            'project_id' => $project->id,
        ]);

        $response = $this
            ->actingAs($manager, 'api')
            ->postJson("/api/leads/{$lead->id}/shortlist/{$unit->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $unit->id);

        $this->assertDatabaseHas('lead_unit', [
            'lead_id' => $lead->id,
            'unit_id' => $unit->id,
        ]);
    }

    public function test_adding_same_unit_twice_does_not_create_duplicate(): void
    {
        $manager = User::factory()->create([
            'tenant' => 'tai',
        ]);

        $manager->assignRole('manager');

        $lead = Lead::factory()->create();

        $project = Project::factory()->create();

        $unit = Unit::factory()->create([
            'project_id' => $project->id,
        ]);

        $this
            ->actingAs($manager, 'api')
            ->postJson("/api/leads/{$lead->id}/shortlist/{$unit->id}")
            ->assertOk();

        $this
            ->actingAs($manager, 'api')
            ->postJson("/api/leads/{$lead->id}/shortlist/{$unit->id}")
            ->assertOk();

        $this->assertDatabaseCount('lead_unit', 1);
    }

    public function test_manager_can_view_lead_shortlist(): void
    {
        $manager = User::factory()->create([
            'tenant' => 'tai',
        ]);

        $manager->assignRole('manager');

        $lead = Lead::factory()->create();

        $project = Project::factory()->create();

        $units = Unit::factory()
            ->count(2)
            ->create([
                'project_id' => $project->id,
            ]);

        $lead->units()->attach($units->pluck('id'));

        $response = $this
            ->actingAs($manager, 'api')
            ->getJson("/api/leads/{$lead->id}/shortlist");

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_manager_can_remove_unit_from_lead_shortlist(): void
    {
        $manager = User::factory()->create([
            'tenant' => 'tai',
        ]);

        $manager->assignRole('manager');

        $lead = Lead::factory()->create();

        $project = Project::factory()->create();

        $unit = Unit::factory()->create([
            'project_id' => $project->id,
        ]);

        $lead->units()->attach($unit->id);

        $response = $this
            ->actingAs($manager, 'api')
            ->deleteJson("/api/leads/{$lead->id}/shortlist/{$unit->id}");

        $response
            ->assertOk()
            ->assertJsonPath(
                'message',
                'Unit removed from lead shortlist successfully.',
            );

        $this->assertDatabaseMissing('lead_unit', [
            'lead_id' => $lead->id,
            'unit_id' => $unit->id,
        ]);
    }

    public function test_unauthorized_agent_cannot_modify_another_agents_lead_shortlist(): void
    {
        $agent = User::factory()->create([
            'tenant' => 'tai',
        ]);

        $agent->assignRole('agent');

        $otherAgent = User::factory()->create([
            'tenant' => 'tai',
        ]);

        $otherAgent->assignRole('agent');

        $lead = Lead::factory()->create([
            'agent_id' => $otherAgent->id,
        ]);

        $project = Project::factory()->create();

        $unit = Unit::factory()->create([
            'project_id' => $project->id,
        ]);

        $response = $this
            ->actingAs($agent, 'api')
            ->postJson("/api/leads/{$lead->id}/shortlist/{$unit->id}");

        $response->assertForbidden();

        $this->assertDatabaseMissing('lead_unit', [
            'lead_id' => $lead->id,
            'unit_id' => $unit->id,
        ]);
    }
}
