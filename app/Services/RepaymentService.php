<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NoOutstandingDebtException;
use App\Models\Debt;
use App\Models\Farmer;
use App\Models\Repayment;
use App\Models\RepaymentDebt;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RepaymentService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        return Repayment::with(['farmer' => fn ($q) => $q->withOutstandingDebt(), 'operator'])
            ->latest()
            ->when(isset($filters['farmer_id']), fn ($q) => $q->where('farmer_id', $filters['farmer_id']))
            ->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data, User $operator): Repayment
    {
        return DB::transaction(function () use ($data, $operator) {
            $farmer = Farmer::findOrFail($data['farmer_id']);

            $hasOutstandingDebt = $farmer->debts()
                ->where('remaining_amount', '>', 0)
                ->exists();

            if (! $hasOutstandingDebt) {
                throw new NoOutstandingDebtException;
            }

            $fcfaValue = $data['kg_received'] * $data['commodity_rate'];

            $repayment = Repayment::create([
                'farmer_id' => $farmer->id,
                'operator_id' => $operator->id,
                'kg_received' => $data['kg_received'],
                'commodity_rate' => $data['commodity_rate'],
                'fcfa_value' => $fcfaValue,
            ]);

            $this->applyFifo($repayment, $farmer, $fcfaValue);

            $repayment->load(['farmer', 'operator', 'debts']);
            $repayment->farmer->loadOutstandingDebt();

            return $repayment;
        });
    }

    private function applyFifo(Repayment $repayment, Farmer $farmer, float $fcfaValue): void
    {
        $remaining = $fcfaValue;

        $debts = Debt::where('farmer_id', $farmer->id)
            ->where('remaining_amount', '>', 0)
            ->orderBy('created_at', 'asc')
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
