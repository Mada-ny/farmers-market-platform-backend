<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Farmer;
use Illuminate\Database\Eloquent\Collection;

class FarmerService
{
    public function list(): Collection
    {
        return Farmer::all();
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
