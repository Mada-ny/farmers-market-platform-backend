<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Debt;
use App\Models\Farmer;
use App\Models\Transaction;
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

    public function test_farmer_show_exposes_outstanding_debt_and_available_credit(): void
    {
        $operator = User::factory()->operator()->create();
        $farmer = Farmer::factory()->create(['credit_limit' => 500_000]);

        $transaction = Transaction::factory()->create([
            'farmer_id' => $farmer->id,
            'operator_id' => $operator->id,
        ]);

        Debt::create([
            'transaction_id' => $transaction->id,
            'farmer_id' => $farmer->id,
            'amount_fcfa' => 100_000,
            'remaining_amount' => 100_000,
        ]);

        $this->actingAs($operator)
            ->getJson("/api/v1/farmers/{$farmer->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.outstanding_debt', 100_000)
            ->assertJsonPath('data.available_credit', 400_000);
    }

    public function test_farmer_with_no_debt_has_full_available_credit(): void
    {
        $operator = User::factory()->operator()->create();
        $farmer = Farmer::factory()->create(['credit_limit' => 300_000]);

        $this->actingAs($operator)
            ->getJson("/api/v1/farmers/{$farmer->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.outstanding_debt', 0)
            ->assertJsonPath('data.available_credit', 300_000);
    }

    public function test_farmer_list_includes_outstanding_debt_and_available_credit(): void
    {
        $operator = User::factory()->operator()->create();
        $farmer = Farmer::factory()->create(['credit_limit' => 200_000]);

        $transaction = Transaction::factory()->create([
            'farmer_id' => $farmer->id,
            'operator_id' => $operator->id,
        ]);

        Debt::create([
            'transaction_id' => $transaction->id,
            'farmer_id' => $farmer->id,
            'amount_fcfa' => 50_000,
            'remaining_amount' => 50_000,
        ]);

        $response = $this->actingAs($operator)
            ->getJson('/api/v1/farmers')
            ->assertStatus(200);

        $farmerData = collect($response->json('data'))->firstWhere('id', $farmer->id);

        $this->assertEquals(50_000.0, $farmerData['outstanding_debt']);
        $this->assertEquals(150_000.0, $farmerData['available_credit']);
    }

    public function test_operator_can_search_farmer_by_identifier(): void
    {
        $operator = User::factory()->operator()->create();
        $target = Farmer::factory()->create(['identifier' => 'CI-ABJ-99999']);
        Farmer::factory()->create(['identifier' => 'CI-YAM-00001']);

        $response = $this->actingAs($operator)
            ->getJson('/api/v1/farmers?search=CI-ABJ-99999')
            ->assertStatus(200);

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('CI-ABJ-99999', $response->json('data.0.identifier'));
    }

    public function test_operator_can_search_farmer_by_phone(): void
    {
        $operator = User::factory()->operator()->create();
        $target = Farmer::factory()->create(['phone' => '+2250799999999']);
        Farmer::factory()->create(['phone' => '+2250700000001']);

        $response = $this->actingAs($operator)
            ->getJson('/api/v1/farmers?search=%2B2250799999999')
            ->assertStatus(200);

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('+2250799999999', $response->json('data.0.phone'));
    }

    public function test_farmer_search_returns_empty_when_no_match(): void
    {
        $operator = User::factory()->operator()->create();
        Farmer::factory()->create();

        $response = $this->actingAs($operator)
            ->getJson('/api/v1/farmers?search=NONEXISTENT')
            ->assertStatus(200);

        $this->assertCount(0, $response->json('data'));
    }

    public function test_settled_debts_are_excluded_from_outstanding_debt(): void
    {
        $operator = User::factory()->operator()->create();
        $farmer = Farmer::factory()->create(['credit_limit' => 500_000]);

        $transaction = Transaction::factory()->create([
            'farmer_id' => $farmer->id,
            'operator_id' => $operator->id,
        ]);

        Debt::create([
            'transaction_id' => $transaction->id,
            'farmer_id' => $farmer->id,
            'amount_fcfa' => 80_000,
            'remaining_amount' => 0,
        ]);

        $this->actingAs($operator)
            ->getJson("/api/v1/farmers/{$farmer->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.outstanding_debt', 0)
            ->assertJsonPath('data.available_credit', 500_000);
    }
}
