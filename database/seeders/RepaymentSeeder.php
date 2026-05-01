<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PaymentMethod;
use App\Models\Debt;
use App\Models\Farmer;
use App\Models\Product;
use App\Models\Repayment;
use App\Models\RepaymentDebt;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class RepaymentSeeder extends Seeder
{
    public function run(): void
    {
        $operator = User::where('role', 'operator')->first();
        $farmer = Farmer::where('identifier', 'CI-DAL-00007')->first();

        if (! $operator || ! $farmer) {
            return;
        }

        $productIds = Product::pluck('id', 'name')->all();

        // Three credit transactions → three debts in ascending age order
        $this->makeCreditTransactionWithDebt($farmer, $operator, $productIds, [
            ['Power Sprayer (20L)', 1, 45000],
        ]); // Debt A: credited_amount = 49 500

        $this->makeCreditTransactionWithDebt($farmer, $operator, $productIds, [
            ['Water Pump (2HP)', 2, 55000],
        ]); // Debt B: credited_amount = 121 000

        $this->makeCreditTransactionWithDebt($farmer, $operator, $productIds, [
            ['NPK 15-15-15 (50kg)', 3, 20000],
        ]); // Debt C: credited_amount = 66 000

        // Repayment 1 — fully settles Debt A (75 kg × 660 = 49 500)
        $rep1 = Repayment::create([
            'farmer_id' => $farmer->id,
            'operator_id' => $operator->id,
            'kg_received' => 75,
            'commodity_rate' => 660,
            'fcfa_value' => 49500,
        ]);
        $this->applyFifo($rep1, $farmer);

        // Repayment 2 — partially reduces Debt B (100 kg × 660 = 66 000)
        $rep2 = Repayment::create([
            'farmer_id' => $farmer->id,
            'operator_id' => $operator->id,
            'kg_received' => 100,
            'commodity_rate' => 660,
            'fcfa_value' => 66000,
        ]);
        $this->applyFifo($rep2, $farmer);

        // Repayment 3 — further reduces Debt B (82.73 kg × 660 ≈ 54 600)
        $rep3 = Repayment::create([
            'farmer_id' => $farmer->id,
            'operator_id' => $operator->id,
            'kg_received' => 82.73,
            'commodity_rate' => 660,
            'fcfa_value' => 54600,
        ]);
        $this->applyFifo($rep3, $farmer);

        // Final debt states:
        // Debt A → remaining_amount = 0       (fully settled)
        // Debt B → remaining_amount = 400     (partially open - 121000 - 66000 - 54600 = 400)
        // Debt C → remaining_amount = 66 000  (untouched)
    }

    private function makeCreditTransactionWithDebt(
        Farmer $farmer,
        User $operator,
        array $productIds,
        array $lines,
        float $rate = 10
    ): Debt {
        $total = 0.0;

        foreach ($lines as [, $qty, $price]) {
            $total += $qty * $price;
        }

        $creditedAmount = round($total * (1 + $rate / 100), 2);

        $transaction = Transaction::create([
            'farmer_id' => $farmer->id,
            'operator_id' => $operator->id,
            'total_fcfa' => $total,
            'payment_method' => PaymentMethod::Credit,
            'interest_rate' => $rate,
            'credited_amount' => $creditedAmount,
        ]);

        foreach ($lines as [$name, $qty, $price]) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $productIds[$name],
                'quantity' => $qty,
                'unit_price' => $price,
            ]);
        }

        return Debt::create([
            'transaction_id' => $transaction->id,
            'farmer_id' => $farmer->id,
            'amount_fcfa' => $creditedAmount,
            'remaining_amount' => $creditedAmount,
        ]);
    }

    // Mirrors RepaymentService::applyFifo — secondary orderBy('id') ensures
    // deterministic ordering when created_at timestamps collide (SQLite stores
    // at second resolution). The service uses created_at only; both should match.
    private function applyFifo(Repayment $repayment, Farmer $farmer): void
    {
        $remaining = (float) $repayment->fcfa_value;

        $debts = Debt::where('farmer_id', $farmer->id)
            ->where('remaining_amount', '>', 0)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($debts as $debt) {
            if ($remaining <= 0) {
                break;
            }

            $applied = min($remaining, (float) $debt->remaining_amount);

            $debt->decrement('remaining_amount', $applied);

            RepaymentDebt::create([
                'repayment_id' => $repayment->id,
                'debt_id' => $debt->id,
                'amount_applied' => $applied,
            ]);

            $remaining -= $applied;
        }
    }
}
