<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RepaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'farmer' => new FarmerResource($this->whenLoaded('farmer')),
            'operator' => new UserResource($this->whenLoaded('operator')),
            'kg_received' => $this->kg_received,
            'commodity_rate' => $this->commodity_rate,
            'fcfa_value' => $this->fcfa_value,
            'debts_settled' => DebtResource::collection($this->whenLoaded('debts')),
            'created_at' => $this->created_at,
        ];
    }
}
