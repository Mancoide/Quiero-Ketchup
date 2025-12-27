<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="CmsBanner",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="section_id", type="integer", example=1),
 *   @OA\Property(property="button_text", type="string", nullable=true, example="Ver menú"),
 *   @OA\Property(property="image_link", type="string", nullable=true, example="https://example.com"),
 *   @OA\Property(property="button_link", type="string", nullable=true, example="https://example.com"),
 *   @OA\Property(property="status", type="string", example="active"),
 *   @OA\Property(property="status_label", type="string", example="Activo"),
 *   @OA\Property(property="sort_order", type="integer", example=1),
 *   @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/storage/...jpg")
 * )
 */
class BannerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $status = $this->status;

        return [
            'id' => $this->id,
            'section_id' => $this->section_id,
            'button_text' => $this->button_text,
            'image_link' => $this->image_link,
            'button_link' => $this->button_link,
            'status' => $status?->value,
            'status_label' => $status?->label(),
            'sort_order' => $this->sort_order,
            'image_url' => $this->getImageUrl() ?: null,
        ];
    }
}
