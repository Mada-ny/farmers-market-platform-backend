<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson('/api/v1/categories', [
            'name' => 'Céréales',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Céréales');
    }

    public function test_supervisor_can_create_category(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $response = $this->actingAs($supervisor)->postJson('/api/v1/categories', [
            'name' => 'Légumes',
        ]);

        $response->assertStatus(201);
    }

    public function test_operator_cannot_create_category(): void
    {
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)
            ->postJson('/api/v1/categories', ['name' => 'Fruits'])
            ->assertStatus(403);
    }
}
