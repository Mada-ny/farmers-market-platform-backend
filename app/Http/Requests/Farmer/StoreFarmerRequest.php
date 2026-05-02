<?php

declare(strict_types=1);

namespace App\Http\Requests\Farmer;

use Illuminate\Foundation\Http\FormRequest;

class StoreFarmerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'unique:farmers,identifier'],
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'unique:farmers,phone', 'regex:/^\+225\d{10}$/'],
            'credit_limit' => ['required', 'numeric', 'min:0'],
        ];
    }
}
