<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="Category",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Hamburguesas"),
 *   @OA\Property(property="slug", type="string", example="hamburguesas"),
 *   @OA\Property(property="description", type="string", nullable=true, example=""),
 *   @OA\Property(property="meta", type="object", nullable=true),
 *   @OA\Property(
 *     property="subcategories",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/Subcategory")
 *   )
 * )
 */
class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'meta' => $this->meta,
            'subcategories' => SubcategoryResource::collection($this->whenLoaded('subcategories')),
        ];
    }
}
