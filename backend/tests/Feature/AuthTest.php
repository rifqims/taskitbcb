<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['email' => 'a@test.com']);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'a@test.com',
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['data' => ['token', 'user' => ['id', 'email', 'role']]]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create(['email' => 'a@test.com']);

        $this->postJson('/api/auth/login', [
            'email' => 'a@test.com',
            'password' => 'salah',
        ])->assertStatus(422);
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create(['email' => 'a@test.com', 'is_active' => false]);

        $this->postJson('/api/auth/login', [
            'email' => 'a@test.com',
            'password' => 'password',
        ])->assertStatus(422);
    }

    public function test_authenticated_user_can_fetch_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_guest_cannot_fetch_profile(): void
    {
        $this->getJson('/api/auth/me')->assertStatus(401);
    }
}
