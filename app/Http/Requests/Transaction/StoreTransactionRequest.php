<?php

declare(strict_types=1);

namespace App\Http\Requests\Transaction;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'farmer_id' => ['required', 'integer', 'exists:farmers,id'],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'interest_rate' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'interest_rate.max' => 'interest_rate must be a percentage between 0 and 100 (e.g. send 10 for 10%, 30 for 30%).',
            'interest_rate.min' => 'interest_rate must be a percentage between 0 and 100 (e.g. send 10 for 10%, 30 for 30%).',
        ];
    }
}
