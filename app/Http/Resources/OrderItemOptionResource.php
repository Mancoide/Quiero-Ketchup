<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="OrderItemOption",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="product_option_item_id", type="integer", nullable=true, example=10),
 *   @OA\Property(property="name", type="string", example="Extra queso"),
 *   @OA\Property(property="price", type="number", format="float", example=1000),
 *   @OA\Property(property="meta", type="object", nullable=true)
 * )
 */
class OrderItemOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_option_item_id' => $this->product_option_item_id,
            'name' => $this->name,
            'price' => $this->price,
            'meta' => $this->meta,
        ];
    }
}
