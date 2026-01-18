<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="Order",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="fulfillment_type", type="string", example="delivery", description="Tipo de entrega: delivery, pickup, dine_in"),
 *   @OA\Property(property="status", type="string", example="pending"),
 *   @OA\Property(property="total_amount", type="number", format="float", example=31000),
 *   @OA\Property(property="currency", type="string", example="PYG"),
 *   @OA\Property(property="metadata", type="object", nullable=true),
 *   @OA\Property(property="restaurant", ref="#/components/schemas/Restaurant"),
 *   @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/OrderItem")),
 *   @OA\Property(property="created_at", type="string", format="date-time")
 * )
 */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fulfillment_type' => $this->fulfillment_type,
            'status' => $this->status,
            'total_amount' => $this->total_amount,
            'currency' => $this->currency,
            'metadata' => $this->metadata,
            'restaurant' => new RestaurantResource($this->whenLoaded('restaurant')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at,
        ];
    }
}
