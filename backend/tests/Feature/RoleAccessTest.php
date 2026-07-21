<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_route(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->getJson('/api/admin/ping')
            ->assertOk()->assertJsonPath('area', 'admin');
    }

    public function test_it_support_cannot_access_admin_route(): void
    {
        $it = User::factory()->itSupport()->create();

        $this->actingAs($it)->getJson('/api/admin/ping')->assertStatus(403);
    }

    public function test_it_support_can_access_it_route(): void
    {
        $it = User::factory()->itSupport()->create();

        $this->actingAs($it)->getJson('/api/it/ping')
            ->assertOk()->assertJsonPath('area', 'it');
    }

    public function test_client_cannot_access_it_route(): void
    {
        $client = User::factory()->client()->create();

        $this->actingAs($client)->getJson('/api/it/ping')->assertStatus(403);
    }

    public function test_inactive_user_is_blocked_by_role_middleware(): void
    {
        $admin = User::factory()->admin()->create(['is_active' => false]);

        $this->actingAs($admin)->getJson('/api/admin/ping')->assertStatus(403);
    }
}
