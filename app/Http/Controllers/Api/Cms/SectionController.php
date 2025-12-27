<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Cms\SectionResource;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends ApiController
{
    /**
     * @OA\Get(
     *   path="/api/sections",
     *   tags={"CMS"},
     *   summary="Listar secciones (CMS)",
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=15)),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CmsSection"))
     *     )
     *   )
     * )
     */
    public function index(Request $request)
    {
        $sections = $this->paginate(
            $request,
            Section::query()->with(['banners', 'internalPages'])->orderBy('name'),
        );

        return SectionResource::collection($sections);
    }

    /**
     * @OA\Get(
     *   path="/api/sections/{id}",
     *   tags={"CMS"},
     *   summary="Ver una sección (CMS)",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/CmsSection")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Section $section)
    {
        $section->load(['banners', 'internalPages']);

        return new SectionResource($section);
    }
}
