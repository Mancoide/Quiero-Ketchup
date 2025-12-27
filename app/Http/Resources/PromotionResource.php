<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="Promotion",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="code", type="string", nullable=true, example="PROMO10"),
 *   @OA\Property(property="type", type="string", nullable=true, example="percentage"),
 *   @OA\Property(property="value", type="number", format="float", example=10),
 *   @OA\Property(property="starts_at", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="ends_at", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="meta", type="object", nullable=true)
 * )
 */
class PromotionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->type,
            'value' => $this->value,
            'starts_at' => $this->starts_at?->toISOString(),
            'ends_at' => $this->ends_at?->toISOString(),
            'meta' => $this->meta,
        ];
    }
}
