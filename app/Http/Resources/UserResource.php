<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Juan Pérez"),
 *   @OA\Property(property="email", type="string", example="juan@example.com"),
 *   @OA\Property(property="status", type="string", example="active"),
 *   @OA\Property(property="avatar_url", type="string", nullable=true),
 *   @OA\Property(property="roles", type="array", @OA\Items(type="string")),
 *   @OA\Property(property="addresses", type="array", @OA\Items(ref="#/components/schemas/Address"))
 * )
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $avatarUrl = null;

        if (method_exists($this->resource, 'getAvatarUrl')) {
            $avatarUrl = $this->getAvatarUrl();
        }

        $addresses = method_exists($this->resource, 'addresses')
            ? $this->addresses()->orderByDesc('id')->get()
            : collect();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status?->value,
            'avatar_url' => $avatarUrl,
            'roles' => method_exists($this->resource, 'getRoleNames') ? $this->getRoleNames()->values()->all() : [],
            'addresses' => AddressResource::collection($addresses),
        ];
    }
}
