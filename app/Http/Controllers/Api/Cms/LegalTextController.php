<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Cms\LegalTextResource;
use App\Models\LegalText;
use Illuminate\Http\Request;

class LegalTextController extends ApiController
{
    /**
     * @OA\Get(
     *   path="/api/legal-texts",
     *   tags={"CMS"},
     *   summary="Listar textos legales (CMS)",
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=15)),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CmsLegalText"))
     *     )
     *   )
     * )
     */
    public function index(Request $request)
    {
        $texts = $this->paginate(
            $request,
            LegalText::query()->orderBy('type'),
        );

        return LegalTextResource::collection($texts);
    }

    /**
     * @OA\Get(
     *   path="/api/legal-texts/{id}",
     *   tags={"CMS"},
     *   summary="Ver un texto legal (CMS)",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/CmsLegalText")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(LegalText $legalText)
    {
        return new LegalTextResource($legalText);
    }
}
