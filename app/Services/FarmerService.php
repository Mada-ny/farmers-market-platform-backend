<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Role;
use App\Models\Farmer;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FarmerService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        return Farmer::withOutstandingDebt()->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data): Farmer
    {
        return Farmer::create($data);
    }

    public function update(Farmer $farmer, array $data): Farmer
    {
        $farmer->update($data);

        return $farmer->fresh();
    }

    public function delete(Farmer $farmer): void
    {
        $farmer->delete();
    }

    public function outstandingDebts(Farmer $farmer, ?User $viewer = null): Collection
    {
        return $farmer->debts()
            ->when(
                $viewer?->role === Role::Operator,
                fn ($q) => $q->whereHas('transaction', fn ($q2) => $q2->where('operator_id', $viewer->id))
            )
            ->where('remaining_amount', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
