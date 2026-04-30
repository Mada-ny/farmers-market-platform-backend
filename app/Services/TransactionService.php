<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Enums\Role;
use App\Exceptions\CreditLimitExceededException;
use App\Models\Debt;
use App\Models\Farmer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function list(array $filters = [], ?User $viewer = null): LengthAwarePaginator
    {
        return Transaction::with(['farmer' => fn ($q) => $q->withOutstandingDebt(), 'operator'])
            ->latest()
            ->when($viewer?->role === Role::Operator, fn ($q) => $q->where('operator_id', $viewer->id))
            ->when(isset($filters['farmer_id']), fn ($q) => $q->where('farmer_id', $filters['farmer_id']))
            ->when(isset($filters['payment_method']), fn ($q) => $q->where('payment_method', $filters['payment_method']))
            ->when(isset($filters['date_from']), fn ($q) => $q->whereDate('created_at', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn ($q) => $q->whereDate('created_at', '<=', $filters['date_to']))
            ->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data, User $operator): Transaction
    {
        return DB::transaction(function () use ($data, $operator) {
            $farmer = Farmer::findOrFail($data['farmer_id']);
            $items = $data['items'];

            $totalFcfa = collect($items)->sum(
                fn (array $item) => $item['quantity'] * $item['unit_price']
            );

            $interestRate = null;
            $creditedAmount = null;

            if ($data['payment_method'] === PaymentMethod::Credit->value) {
                $interestRate = $data['interest_rate'] ?? config('business.interest_rate');
                $creditedAmount = $totalFcfa * (1 + $interestRate);

                $this->enforceCreditLimit($farmer, $creditedAmount);
            }

            $transaction = Transaction::create([
                'farmer_id' => $farmer->id,
                'operator_id' => $operator->id,
                'total_fcfa' => $totalFcfa,
                'payment_method' => $data['payment_method'],
                'interest_rate' => $interestRate,
                'credited_amount' => $creditedAmount,
            ]);

            foreach ($items as $item) {
                $transaction->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            if ($data['payment_method'] === PaymentMethod::Credit->value) {
                Debt::create([
                    'transaction_id' => $transaction->id,
                    'farmer_id' => $farmer->id,
                    'amount_fcfa' => $creditedAmount,
                    'remaining_amount' => $creditedAmount,
                ]);
            }

            $transaction->load(['farmer', 'operator', 'items.product', 'debt']);

            $transaction->farmer->loadOutstandingDebt();

            return $transaction;
        });
    }

    private function enforceCreditLimit(Farmer $farmer, float $newCreditedAmount): void
    {
        $outstanding = $farmer->debts()
            ->where('remaining_amount', '>', 0)
            ->sum('remaining_amount');

        if (($outstanding + $newCreditedAmount) > $farmer->credit_limit) {
            throw new CreditLimitExceededException;
        }
    }
}
