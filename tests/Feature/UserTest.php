<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_supervisor(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson('/api/v1/users', [
            'name' => 'New Supervisor',
            'email' => 'supervisor@test.com',
            'password' => 'password123',
            'role' => 'supervisor',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.role', 'supervisor');
    }

    public function test_supervisor_can_create_an_operator(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $response = $this->actingAs($supervisor)->postJson('/api/v1/users', [
            'name' => 'New Operator',
            'email' => 'operator@test.com',
            'password' => 'password123',
            'role' => 'operator',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.role', 'operator');
    }

    public function test_operator_cannot_access_users(): void
    {
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)
            ->getJson('/api/v1/users')
            ->assertStatus(403);
    }

    public function test_admin_cannot_create_an_operator(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson('/api/v1/users', [
            'name' => 'Rogue Operator',
            'email' => 'rogue@test.com',
            'password' => 'password123',
            'role' => 'operator',
        ])->assertStatus(422);
    }

    public function test_supervisor_cannot_create_a_supervisor(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $this->actingAs($supervisor)->postJson('/api/v1/users', [
            'name' => 'Another Supervisor',
            'email' => 'another@test.com',
            'password' => 'password123',
            'role' => 'supervisor',
        ])->assertStatus(422);
    }

    public function test_admin_list_only_returns_supervisors(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->supervisor()->create();
        User::factory()->operator()->create();

        $response = $this->actingAs($admin)->getJson('/api/v1/users');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 1);
    }

    public function test_supervisor_list_only_returns_operators(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        User::factory()->admin()->create();
        User::factory()->operator()->create();

        $response = $this->actingAs($supervisor)->getJson('/api/v1/users');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 1);
    }

    public function test_admin_cannot_view_an_operator(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->operator()->create();

        $this->actingAs($admin)
            ->getJson("/api/v1/users/{$operator->id}")
            ->assertStatus(403);
    }

    public function test_supervisor_cannot_update_another_supervisor(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $otherSupervisor = User::factory()->supervisor()->create();

        $this->actingAs($supervisor)
            ->putJson("/api/v1/users/{$otherSupervisor->id}", ['name' => 'Hacked'])
            ->assertStatus(403);
    }

    public function test_admin_cannot_delete_an_operator(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->operator()->create();

        $this->actingAs($admin)
            ->deleteJson("/api/v1/users/{$operator->id}")
            ->assertStatus(403);
    }
}
