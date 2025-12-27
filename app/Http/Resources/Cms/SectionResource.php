<?php

namespace App\Http\Resources\Cms;

use App\Http\Resources\Cms\BannerResource;
use App\Http\Resources\Cms\InternalPageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="CmsSection",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Home"),
 *   @OA\Property(property="description", type="string", nullable=true),
 *   @OA\Property(property="status", type="string", example="active"),
 *   @OA\Property(property="status_label", type="string", example="Activo"),
 *   @OA\Property(property="banners", type="array", @OA\Items(ref="#/components/schemas/CmsBanner")),
 *   @OA\Property(property="internal_pages", type="array", @OA\Items(ref="#/components/schemas/CmsInternalPage"))
 * )
 */
class SectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $status = $this->status;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $status?->value,
            'status_label' => $status?->label(),
            'banners' => BannerResource::collection($this->whenLoaded('banners')),
            'internal_pages' => InternalPageResource::collection($this->whenLoaded('internalPages')),
        ];
    }
}
