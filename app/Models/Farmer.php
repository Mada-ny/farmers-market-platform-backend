<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farmer extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'firstname',
        'lastname',
        'phone',
        'credit_limit',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit' => 'decimal:2',
        ];
    }

    public function scopeWithOutstandingDebt(Builder $query): Builder
    {
        return $query->withSum(
            ['debts as outstanding_debt' => fn ($q) => $q->where('remaining_amount', '>', 0)],
            'remaining_amount'
        );
    }

    public function loadOutstandingDebt(): static
    {
        return $this->loadSum(
            ['debts as outstanding_debt' => fn ($q) => $q->where('remaining_amount', '>', 0)],
            'remaining_amount'
        );
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(Repayment::class);
    }
}
