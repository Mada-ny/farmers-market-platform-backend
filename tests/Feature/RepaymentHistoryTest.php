<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Debt;
use App\Models\Farmer;
use App\Models\Repayment;
use App\Models\RepaymentDebt;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepaymentHistoryTest extends TestCase
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

    private function createRepaymentWithDebt(float $debtAmount, float $kgReceived, float $rate): Repayment
    {
        $transaction = Transaction::factory()->create([
            'farmer_id' => $this->farmer->id,
            'operator_id' => $this->operator->id,
        ]);

        $debt = Debt::create([
            'transaction_id' => $transaction->id,
            'farmer_id' => $this->farmer->id,
            'amount_fcfa' => $debtAmount,
            'remaining_amount' => $debtAmount,
        ]);

        $fcfaValue = $kgReceived * $rate;

        $repayment = Repayment::create([
            'farmer_id' => $this->farmer->id,
            'operator_id' => $this->operator->id,
            'kg_received' => $kgReceived,
            'commodity_rate' => $rate,
            'fcfa_value' => $fcfaValue,
        ]);

        $applied = min($fcfaValue, $debtAmount);

        RepaymentDebt::create([
            'repayment_id' => $repayment->id,
            'debt_id' => $debt->id,
            'amount_applied' => $applied,
        ]);

        $debt->decrement('remaining_amount', $applied);

        return $repayment;
    }

    // ─── Index ────────────────────────────────────────────────────────────────

    public function test_operator_can_list_repayments(): void
    {
        $this->createRepaymentWithDebt(10_000, 10, 500);
        $this->createRepaymentWithDebt(5_000, 5, 500);

        $this->actingAs($this->operator)
            ->getJson('/api/v1/repayments')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonPath('meta.total', 2);
    }

    public function test_repayment_list_includes_farmer_and_operator(): void
    {
        $this->createRepaymentWithDebt(10_000, 10, 500);

        $response = $this->actingAs($this->operator)
            ->getJson('/api/v1/repayments')
            ->assertStatus(200);

        $item = $response->json('data.0');

        $this->assertArrayHasKey('farmer', $item);
        $this->assertArrayHasKey('operator', $item);
        $this->assertArrayNotHasKey('debts_settled', $item);
    }

    public function test_repayment_list_filter_by_farmer_id(): void
    {
        $otherFarmer = Farmer::factory()->create(['credit_limit' => 500_000]);

        $this->createRepaymentWithDebt(10_000, 10, 500);

        $transaction = Transaction::factory()->create([
            'farmer_id' => $otherFarmer->id,
            'operator_id' => $this->operator->id,
        ]);
        $debt = Debt::create([
            'transaction_id' => $transaction->id,
            'farmer_id' => $otherFarmer->id,
            'amount_fcfa' => 5_000,
            'remaining_amount' => 5_000,
        ]);
        $repayment = Repayment::create([
            'farmer_id' => $otherFarmer->id,
            'operator_id' => $this->operator->id,
            'kg_received' => 5,
            'commodity_rate' => 500,
            'fcfa_value' => 2_500,
        ]);
        RepaymentDebt::create(['repayment_id' => $repayment->id, 'debt_id' => $debt->id, 'amount_applied' => 2_500]);

        $response = $this->actingAs($this->operator)
            ->getJson("/api/v1/repayments?farmer_id={$this->farmer->id}")
            ->assertStatus(200);

        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals($this->farmer->id, $response->json('data.0.farmer.id'));
    }

    public function test_repayment_list_per_page_is_respected(): void
    {
        $this->createRepaymentWithDebt(10_000, 5, 500);
        $this->createRepaymentWithDebt(10_000, 5, 500);
        $this->createRepaymentWithDebt(10_000, 5, 500);

        $response = $this->actingAs($this->operator)
            ->getJson('/api/v1/repayments?per_page=2')
            ->assertStatus(200);

        $this->assertCount(2, $response->json('data'));
        $this->assertEquals(3, $response->json('meta.total'));
    }

    public function test_admin_cannot_list_repayments(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->getJson('/api/v1/repayments')
            ->assertStatus(403);
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function test_operator_can_get_repayment_detail(): void
    {
        $repayment = $this->createRepaymentWithDebt(10_000, 10, 500);

        $this->actingAs($this->operator)
            ->getJson("/api/v1/repayments/{$repayment->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $repayment->id)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'farmer',
                    'operator',
                    'kg_received',
                    'commodity_rate',
                    'fcfa_value',
                    'debts_settled',
                    'created_at',
                ],
            ]);
    }

    public function test_repayment_show_includes_amount_applied_on_each_debt(): void
    {
        $repayment = $this->createRepaymentWithDebt(10_000, 10, 500);

        $response = $this->actingAs($this->operator)
            ->getJson("/api/v1/repayments/{$repayment->id}")
            ->assertStatus(200);

        $debt = $response->json('data.debts_settled.0');

        $this->assertArrayHasKey('amount_applied', $debt);
        $this->assertEquals(5_000, $debt['amount_applied']);
    }

    public function test_repayment_show_returns_404_for_unknown_id(): void
    {
        $this->actingAs($this->operator)
            ->getJson('/api/v1/repayments/9999')
            ->assertStatus(404);
    }
}
