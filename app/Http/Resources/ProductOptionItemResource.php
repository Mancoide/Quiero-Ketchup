<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="ProductOptionItem",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="product_option_id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Queso extra"),
 *   @OA\Property(property="price", type="number", format="float", example=5.00),
 *   @OA\Property(property="meta", type="object", nullable=true)
 * )
 */
class ProductOptionItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_option_id' => $this->product_option_id,
            'name' => $this->name,
            'price' => $this->price,
            'meta' => $this->meta,
        ];
    }
}
