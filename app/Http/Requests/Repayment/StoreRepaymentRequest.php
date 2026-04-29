<?php

declare(strict_types=1);

namespace App\Http\Requests\Repayment;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'farmer_id' => ['required', 'integer', 'exists:farmers,id'],
            'kg_received' => ['required', 'numeric', 'min:0.01'],
            'commodity_rate' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
