<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FarmerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $outstandingDebt = (float) ($this->outstanding_debt ?? 0);

        return [
            'id' => $this->id,
            'identifier' => $this->identifier,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'phone' => $this->phone,
            'credit_limit' => $this->credit_limit,
            'outstanding_debt' => $outstandingDebt,
            'available_credit' => (float) $this->credit_limit - $outstandingDebt,
        ];
    }
}
