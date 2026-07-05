<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Wraps a plain associative array of aggregate statistics. Deliberately
 * simple for Fase 6 — Fase 9 (non-functional) will add response caching on
 * top of the controller that builds this array, without needing to change
 * this Resource.
 */
class DashboardStatResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return is_array($this->resource) ? $this->resource : (array) $this->resource;
    }
}
