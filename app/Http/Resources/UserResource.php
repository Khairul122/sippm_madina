<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'nik' => $this->nik,
            'phone' => $this->phone,
            'is_active' => (bool) $this->is_active,
            'opd_id' => $this->opd_id,
            'kecamatan_id' => $this->kecamatan_id,
            'roles' => $this->when(method_exists($this->resource, 'getRoleNames'), fn () => $this->getRoleNames()),
            'created_at' => (string) $this->created_at,
        ];
    }
}
