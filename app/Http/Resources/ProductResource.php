<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="Product",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="category_id", type="integer", example=1),
 *   @OA\Property(property="subcategory_id", type="integer", nullable=true, example=10),
 *   @OA\Property(property="name", type="string", example="Hamburguesa Clásica"),
 *   @OA\Property(property="slug", type="string", example="hamburguesa-clasica"),
 *   @OA\Property(property="description", type="string", nullable=true),
 *   @OA\Property(property="price", type="number", format="float", example=49.90),
 *   @OA\Property(property="currency", type="string", nullable=true, example="PYG"),
 *   @OA\Property(property="available", type="boolean", example=true),
 *   @OA\Property(property="meta", type="object", nullable=true),
 *   @OA\Property(property="images", type="array", @OA\Items(type="string")),
 *   @OA\Property(property="category", ref="#/components/schemas/Category"),
 *   @OA\Property(property="subcategory", ref="#/components/schemas/Subcategory"),
 *   @OA\Property(property="restaurants", type="array", @OA\Items(ref="#/components/schemas/Restaurant")),
 *   @OA\Property(property="options", type="array", @OA\Items(ref="#/components/schemas/ProductOption")),
 *   @OA\Property(property="promotions", type="array", @OA\Items(ref="#/components/schemas/Promotion"))
 * )
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $images = [];

        if (method_exists($this->resource, 'getMedia')) {
            $images = $this->getMedia('images')
                ->map(fn ($media) => $media->getUrl())
                ->values()
                ->all();
        }

        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'subcategory_id' => $this->subcategory_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'available' => (bool) $this->available,
            'meta' => $this->meta,
            'images' => $images,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'subcategory' => new SubcategoryResource($this->whenLoaded('subcategory')),
            'restaurants' => RestaurantResource::collection($this->whenLoaded('restaurants')),
            'options' => ProductOptionResource::collection($this->whenLoaded('options')),
            'promotions' => PromotionResource::collection($this->whenLoaded('promotions')),
        ];
    }
}
