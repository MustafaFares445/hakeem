<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AuthResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /** @example "1|abc123token..." */
            'token' => $this->resource['token'] ?? null,
            /** @example "Bearer" */
            'tokenType' => 'Bearer',
            'user' => UserResource::make($this->resource['user']),
        ];
    }
}
