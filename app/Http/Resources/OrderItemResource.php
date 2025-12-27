<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="OrderItem",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="product", type="object",
 *     @OA\Property(property="id", type="integer", example=10),
 *     @OA\Property(property="name", type="string", example="Hamburguesa"),
 *     @OA\Property(property="price", type="number", format="float", example=15000),
 *     @OA\Property(property="currency", type="string", example="PYG")
 *   ),
 *   @OA\Property(property="quantity", type="integer", example=2),
 *   @OA\Property(property="unit_price", type="number", format="float", example=15000),
 *   @OA\Property(property="total_price", type="number", format="float", example=31000),
 *   @OA\Property(property="meta", type="object", nullable=true),
 *   @OA\Property(property="options", type="array", @OA\Items(ref="#/components/schemas/OrderItemOption"))
 * )
 */
class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->whenLoaded('product');

        return [
            'id' => $this->id,
            'product' => $product ? [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'currency' => $product->currency,
            ] : null,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
            'meta' => $this->meta,
            'options' => OrderItemOptionResource::collection($this->whenLoaded('options')),
        ];
    }
}
