<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Farmer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $operator;

    private Farmer $farmer;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operator = User::factory()->operator()->create();
        $this->farmer = Farmer::factory()->create(['credit_limit' => 500000]);
        $this->product = Product::factory()->create(['price' => 1000]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'farmer_id' => $this->farmer->id,
            'payment_method' => 'cash',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'unit_price' => 1000,
                ],
            ],
        ], $overrides);
    }

    public function test_cash_transaction_creates_no_debt(): void
    {
        $response = $this->actingAs($this->operator)
            ->postJson('/api/v1/transactions', $this->payload());

        $response->assertStatus(201);

        $this->assertDatabaseCount('debts', 0);
    }

    public function test_credit_transaction_creates_debt_with_interest(): void
    {
        $response = $this->actingAs($this->operator)
            ->postJson('/api/v1/transactions', $this->payload([
                'payment_method' => 'credit',
                'interest_rate' => 0.10,
            ]));

        $response->assertStatus(201);

        $this->assertDatabaseCount('debts', 1);

        $this->assertDatabaseHas('debts', [
            'farmer_id' => $this->farmer->id,
            'amount_fcfa' => 2200.00, // 2000 × 1.10
            'remaining_amount' => 2200.00,
        ]);
    }

    public function test_credit_transaction_blocked_when_limit_exceeded(): void
    {
        $farmer = Farmer::factory()->create(['credit_limit' => 1000]);

        $response = $this->actingAs($this->operator)
            ->postJson('/api/v1/transactions', $this->payload([
                'farmer_id' => $farmer->id,
                'payment_method' => 'credit',
                'interest_rate' => 0.10,
                'items' => [
                    [
                        'product_id' => $this->product->id,
                        'quantity' => 2,
                        'unit_price' => 1000,
                    ],
                ],
            ]));

        // credited_amount = 2000 × 1.10 = 2200 > credit_limit 1000
        $response->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseCount('debts', 0);
    }

    public function test_credit_transaction_within_limit_succeeds(): void
    {
        $farmer = Farmer::factory()->create(['credit_limit' => 10000]);

        $response = $this->actingAs($this->operator)
            ->postJson('/api/v1/transactions', $this->payload([
                'farmer_id' => $farmer->id,
                'payment_method' => 'credit',
                'interest_rate' => 0.10,
            ]));

        $response->assertStatus(201);
        $this->assertDatabaseCount('debts', 1);
    }

    public function test_credit_transaction_response_includes_updated_outstanding_debt(): void
    {
        $response = $this->actingAs($this->operator)
            ->postJson('/api/v1/transactions', $this->payload([
                'payment_method' => 'credit',
                'interest_rate' => 0.10,
            ]));

        $response->assertStatus(201)
            ->assertJsonPath('data.farmer.outstanding_debt', 2200)
            ->assertJsonPath('data.farmer.available_credit', 500_000 - 2200);
    }

    public function test_credit_transaction_without_interest_rate_uses_config_default(): void
    {
        config(['business.interest_rate' => 0.15]);

        $response = $this->actingAs($this->operator)
            ->postJson('/api/v1/transactions', $this->payload([
                'payment_method' => 'credit',
            ]));

        $response->assertStatus(201);

        $this->assertDatabaseHas('transactions', [
            'interest_rate' => 0.15,
            'credited_amount' => 2300.00, // 2000 × 1.15
        ]);
    }

    public function test_credit_transaction_with_explicit_interest_rate_overrides_default(): void
    {
        config(['business.interest_rate' => 0.15]);

        $response = $this->actingAs($this->operator)
            ->postJson('/api/v1/transactions', $this->payload([
                'payment_method' => 'credit',
                'interest_rate' => 0.20,
            ]));

        $response->assertStatus(201);

        $this->assertDatabaseHas('transactions', [
            'interest_rate' => 0.20,
            'credited_amount' => 2400.00, // 2000 × 1.20
        ]);
    }

    public function test_operator_only_sees_own_transactions(): void
    {
        $otherOperator = User::factory()->operator()->create();

        Transaction::factory()->create([
            'farmer_id' => $this->farmer->id,
            'operator_id' => $this->operator->id,
        ]);

        Transaction::factory()->create([
            'farmer_id' => $this->farmer->id,
            'operator_id' => $otherOperator->id,
        ]);

        $response = $this->actingAs($this->operator)
            ->getJson('/api/v1/transactions');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 1);
    }

    public function test_operator_cannot_see_other_operators_transactions(): void
    {
        $otherOperator = User::factory()->operator()->create();

        Transaction::factory()->create([
            'farmer_id' => $this->farmer->id,
            'operator_id' => $otherOperator->id,
        ]);

        $response = $this->actingAs($this->operator)
            ->getJson('/api/v1/transactions');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 0);
    }
}
