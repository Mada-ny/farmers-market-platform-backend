<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_id' => $this->transaction_id,
            'amount_fcfa' => $this->amount_fcfa,
            'remaining_amount' => $this->remaining_amount,
            'amount_applied' => $this->whenPivotLoaded('repayment_debt', fn () => $this->pivot->amount_applied),
            'created_at' => $this->created_at,
        ];
    }
}
