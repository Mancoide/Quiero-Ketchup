<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Support\ApiCacheVersion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{
    /**
     * @OA\Get(
     *   path="/api/categories",
     *   tags={"Categorias"},
     *   summary="Listar categorías",
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=15)),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Category"))
     *     )
     *   )
     * )
     */
    public function index(Request $request)
    {
        $version = ApiCacheVersion::get('categories');
        $cacheKey = "api:categories:all:v{$version}";

        $allCategories = Cache::remember($cacheKey, now()->addHours(12), function () {
            return Category::query()
                ->with(['subcategories'])
                ->orderBy('name')
                ->get();
        });

        $categories = $this->paginateCollection($request, $allCategories);

        return CategoryResource::collection($categories);
    }

    /**
     * @OA\Get(
     *   path="/api/categories/{id}",
     *   tags={"Categorias"},
     *   summary="Ver una categoría",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Category")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(int $id)
    {
        $version = ApiCacheVersion::get('categories');
        $cacheKey = "api:categories:{$id}:v{$version}";

        $category = Cache::remember($cacheKey, now()->addHours(12), function () use ($id) {
            return Category::query()
                ->with(['subcategories'])
                ->findOrFail($id);
        });

        return new CategoryResource($category);
    }
}
