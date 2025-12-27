<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="Address",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="street", type="string", example="Av. Siempre Viva 123"),
 *   @OA\Property(property="city", type="string", example="Asunción"),
 *   @OA\Property(property="state", type="string", nullable=true, example=""),
 *   @OA\Property(property="postal_code", type="string", nullable=true, example=""),
 *   @OA\Property(property="country", type="string", example="PY"),
 *   @OA\Property(property="location", type="object", nullable=true),
 *   @OA\Property(property="meta", type="object", nullable=true)
 * )
 */
class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'street' => $this->street,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'location' => $this->location,
            'meta' => $this->meta,
        ];
    }
}
