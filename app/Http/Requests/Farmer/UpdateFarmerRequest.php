<?php

declare(strict_types=1);

namespace App\Http\Requests\Farmer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFarmerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => ['sometimes', 'string', Rule::unique('farmers', 'identifier')->ignore($this->route('farmer'))],
            'firstname' => ['sometimes', 'string', 'max:255'],
            'lastname' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', Rule::unique('farmers', 'phone')->ignore($this->route('farmer')), 'regex:/^\+225\d{10}$/'],
            'credit_limit' => ['sometimes', 'numeric', 'min:0'],
        ];
    }
}
