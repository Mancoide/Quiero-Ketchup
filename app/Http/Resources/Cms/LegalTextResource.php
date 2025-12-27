<?php

namespace App\Http\Resources\Cms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="CmsLegalText",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="type", type="string", example="privacy_policy"),
 *   @OA\Property(property="type_label", type="string", example="Política de privacidad"),
 *   @OA\Property(property="content", type="string")
 * )
 */
class LegalTextResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $type = $this->type;

        return [
            'id' => $this->id,
            'type' => $type?->value,
            'type_label' => $type?->label(),
            'content' => $this->content,
        ];
    }
}
