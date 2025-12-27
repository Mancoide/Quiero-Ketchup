<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Cms\BannerResource;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends ApiController
{
    /**
     * @OA\Get(
     *   path="/api/banners",
     *   tags={"CMS"},
     *   summary="Listar banners (CMS)",
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=15)),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CmsBanner"))
     *     )
     *   )
     * )
     */
    public function index(Request $request)
    {
        $banners = $this->paginate(
            $request,
            Banner::query()->orderBy('sort_order')->orderByDesc('id'),
        );

        return BannerResource::collection($banners);
    }

    /**
     * @OA\Get(
     *   path="/api/banners/{id}",
     *   tags={"CMS"},
     *   summary="Ver un banner (CMS)",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/CmsBanner")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Banner $banner)
    {
        return new BannerResource($banner);
    }
}
