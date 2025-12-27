<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="CmsInternalPage",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="section_id", type="integer", example=1),
 *   @OA\Property(property="title", type="string", example="Quiénes somos"),
 *   @OA\Property(property="description", type="string", nullable=true),
 *   @OA\Property(property="status", type="string", example="active"),
 *   @OA\Property(property="status_label", type="string", example="Activo")
 * )
 */
class InternalPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $status = $this->status;

        return [
            'id' => $this->id,
            'section_id' => $this->section_id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $status?->value,
            'status_label' => $status?->label(),
        ];
    }
}
