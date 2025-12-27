<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Cms\InternalPageResource;
use App\Models\InternalPage;
use Illuminate\Http\Request;

class InternalPageController extends ApiController
{
    /**
     * @OA\Get(
     *   path="/api/internal-pages",
     *   tags={"CMS"},
     *   summary="Listar páginas internas (CMS)",
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=15)),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CmsInternalPage"))
     *     )
     *   )
     * )
     */
    public function index(Request $request)
    {
        $pages = $this->paginate(
            $request,
            InternalPage::query()->orderByDesc('id'),
        );

        return InternalPageResource::collection($pages);
    }

    /**
     * @OA\Get(
     *   path="/api/internal-pages/{id}",
     *   tags={"CMS"},
     *   summary="Ver una página interna (CMS)",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/CmsInternalPage")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(InternalPage $internalPage)
    {
        return new InternalPageResource($internalPage);
    }
}
