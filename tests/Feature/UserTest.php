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
}
