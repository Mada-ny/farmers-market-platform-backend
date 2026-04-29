<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FarmerTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_create_a_farmer(): void
    {
        $operator = User::factory()->operator()->create();

        $response = $this->actingAs($operator)->postJson('/api/v1/farmers', [
            'identifier' => 'FARM-0001',
            'firstname' => 'Kouassi',
            'lastname' => 'Yao',
            'phone' => '+2250700000001',
            'credit_limit' => 300000,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.identifier', 'FARM-0001');
    }

    public function test_admin_cannot_access_farmers(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->getJson('/api/v1/farmers')
            ->assertStatus(403);
    }
}
