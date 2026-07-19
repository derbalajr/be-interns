<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_factory_creates_a_valid_user(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
        ]);

        $this->assertNotSame('password', $user->password);
    }

    public function test_database_seeder_creates_demo_users(): void
    {
        $this->seed();

        $this->assertDatabaseCount('users', 6);

        $this->assertDatabaseHas('users', [
            'name' => 'Demo Manager',
            'email' => 'manager@crm.test',
        ]);
    }

    public function test_user_factory_can_create_an_unverified_user(): void
    {
        $user = User::factory()->unverified()->create();

        $this->assertNull($user->email_verified_at);
    }
}
