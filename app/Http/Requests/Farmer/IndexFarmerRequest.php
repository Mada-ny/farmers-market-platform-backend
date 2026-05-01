<?php

declare(strict_types=1);

namespace App\Http\Requests\Farmer;

use Illuminate\Foundation\Http\FormRequest;

class IndexFarmerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'search' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
