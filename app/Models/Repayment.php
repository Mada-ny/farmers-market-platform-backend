<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Repayment extends Model
{
    protected $fillable = [
        'farmer_id',
        'operator_id',
        'kg_received',
        'commodity_rate',
        'fcfa_value',
    ];

    protected function casts(): array
    {
        return [
            'kg_received' => 'decimal:2',
            'commodity_rate' => 'decimal:2',
            'fcfa_value' => 'decimal:2',
        ];
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function debts(): BelongsToMany
    {
        return $this->belongsToMany(Debt::class, 'repayment_debt')
            ->withPivot('amount_applied')
            ->withTimestamps();
    }
}
