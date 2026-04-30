<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Farmer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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

    public function outstandingDebts(Farmer $farmer): Collection
    {
        return $farmer->debts()
            ->where('remaining_amount', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}
