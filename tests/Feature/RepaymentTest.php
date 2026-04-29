<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Debt;
use App\Models\Farmer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $operator;

    private Farmer $farmer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operator = User::factory()->operator()->create();
        $this->farmer = Farmer::factory()->create(['credit_limit' => 1_000_000]);
    }

    private function createDebt(float $amount, ?string $createdAt = null): Debt
    {
        $transaction = Transaction::create([
            'farmer_id' => $this->farmer->id,
            'operator_id' => $this->operator->id,
            'total_fcfa' => $amount,
            'payment_method' => 'credit',
            'interest_rate' => 0,
            'credited_amount' => $amount,
        ]);

        $debt = Debt::create([
            'transaction_id' => $transaction->id,
            'farmer_id' => $this->farmer->id,
            'amount_fcfa' => $amount,
            'remaining_amount' => $amount,
        ]);

        if ($createdAt) {
            $debt->created_at = $createdAt;
            $debt->save();
        }

        return $debt;
    }

    public function test_repayment_fails_when_farmer_has_no_outstanding_debt(): void
    {
        $this->actingAs($this->operator)
            ->postJson('/api/v1/repayments', [
                'farmer_id' => $this->farmer->id,
                'kg_received' => 10,
                'commodity_rate' => 500,
            ])
            ->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertDatabaseCount('repayments', 0);
    }

    public function test_repayment_applies_fifo_to_oldest_debt_first(): void
    {
        $old = $this->createDebt(5000, '2026-01-01 00:00:00');
        $new = $this->createDebt(5000, '2026-02-01 00:00:00');

        $this->actingAs($this->operator)
            ->postJson('/api/v1/repayments', [
                'farmer_id' => $this->farmer->id,
                'kg_received' => 10,
                'commodity_rate' => 300, // 3000 FCFA
            ])
            ->assertStatus(201);

        // La dette la plus ancienne doit être partiellement réduite
        $this->assertDatabaseHas('debts', [
            'id' => $old->id,
            'remaining_amount' => 2000.00, // 5000 - 3000
        ]);

        // La dette récente est intacte
        $this->assertDatabaseHas('debts', [
            'id' => $new->id,
            'remaining_amount' => 5000.00,
        ]);
    }

    public function test_partial_repayment_does_not_delete_debt(): void
    {
        $debt = $this->createDebt(10000);

        $this->actingAs($this->operator)
            ->postJson('/api/v1/repayments', [
                'farmer_id' => $this->farmer->id,
                'kg_received' => 5,
                'commodity_rate' => 500, // 2500 FCFA
            ])
            ->assertStatus(201);

        $this->assertDatabaseHas('debts', [
            'id' => $debt->id,
            'remaining_amount' => 7500.00,
        ]);

        $this->assertDatabaseCount('debts', 1);
    }

    public function test_repayment_covers_multiple_debts_in_cascade(): void
    {
        $first = $this->createDebt(1000, '2026-01-01 00:00:00');
        $second = $this->createDebt(1000, '2026-02-01 00:00:00');
        $third = $this->createDebt(1000, '2026-03-01 00:00:00');

        // 2500 FCFA couvre entièrement les 2 premières dettes et partiellement la 3e
        $this->actingAs($this->operator)
            ->postJson('/api/v1/repayments', [
                'farmer_id' => $this->farmer->id,
                'kg_received' => 5,
                'commodity_rate' => 500,
            ])
            ->assertStatus(201);

        $this->assertDatabaseHas('debts', ['id' => $first->id,  'remaining_amount' => 0.00]);
        $this->assertDatabaseHas('debts', ['id' => $second->id, 'remaining_amount' => 0.00]);
        $this->assertDatabaseHas('debts', ['id' => $third->id,  'remaining_amount' => 500.00]);

        $this->assertDatabaseCount('repayment_debt', 3);
    }
}
