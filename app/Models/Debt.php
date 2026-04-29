<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Debt extends Model
{
    protected $fillable = [
        'transaction_id',
        'farmer_id',
        'amount_fcfa',
        'remaining_amount',
    ];

    protected function casts(): array
    {
        return [
            'amount_fcfa' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function repayments(): BelongsToMany
    {
        return $this->belongsToMany(Repayment::class, 'repayment_debt')
            ->withPivot('amount_applied')
            ->withTimestamps();
    }
}
