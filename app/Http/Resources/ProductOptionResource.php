<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="ProductOption",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="product_id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Extras"),
 *   @OA\Property(property="type", type="string", nullable=true, example="multiple"),
 *   @OA\Property(property="required", type="boolean", example=false),
 *   @OA\Property(property="meta", type="object", nullable=true),
 *   @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/ProductOptionItem"))
 * )
 */
class ProductOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'type' => $this->type,
            'required' => (bool) $this->required,
            'meta' => $this->meta,
            'items' => ProductOptionItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
