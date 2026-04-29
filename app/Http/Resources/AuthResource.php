<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data' => [
                'token' => $this->resource['token'],
                'user' => new UserResource($this->resource['user']),
            ],
            'message' => 'Login successful.',
        ];
    }
}
