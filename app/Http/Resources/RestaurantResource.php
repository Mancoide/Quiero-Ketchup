<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="Restaurant",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Sucursal Centro"),
 *   @OA\Property(property="slug", type="string", example="sucursal-centro"),
 *   @OA\Property(property="description", type="string", nullable=true),
 *   @OA\Property(property="phone", type="string", nullable=true),
 *   @OA\Property(property="email", type="string", nullable=true),
 *   @OA\Property(property="status", type="string", nullable=true),
 *   @OA\Property(property="settings", type="object", nullable=true),
 *   @OA\Property(property="meta", type="object", nullable=true)
 * )
 */
class RestaurantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'phone' => $this->phone,
            'email' => $this->email,
            'status' => $this->status,
            'settings' => $this->settings,
            'meta' => $this->meta,
        ];
    }
}
