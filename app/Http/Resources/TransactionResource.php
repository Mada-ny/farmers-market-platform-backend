<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'farmer' => new FarmerResource($this->whenLoaded('farmer')),
            'operator' => new UserResource($this->whenLoaded('operator')),
            'total_fcfa' => $this->total_fcfa,
            'payment_method' => $this->payment_method->value,
            'interest_rate' => $this->interest_rate,
            'credited_amount' => $this->credited_amount,
            'items' => TransactionItemResource::collection($this->whenLoaded('items')),
            'debt' => new DebtResource($this->whenLoaded('debt')),
            'created_at' => $this->created_at,
        ];
    }
}
