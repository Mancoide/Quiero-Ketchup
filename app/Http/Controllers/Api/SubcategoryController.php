<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SubcategoryResource;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends ApiController
{
    /**
     * @OA\Get(
     *   path="/api/subcategories",
     *   tags={"Subcategorias"},
     *   summary="Listar subcategorías",
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=15)),
     *   @OA\Parameter(name="category_id", in="query", required=false, @OA\Schema(type="integer")),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Subcategory"))
     *     )
     *   )
     * )
     */
    public function index(Request $request)
    {
        $query = Subcategory::query()->orderBy('name');

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->input('category_id'));
        }

        $subcategories = $this->paginate($request, $query);

        return SubcategoryResource::collection($subcategories);
    }

    /**
     * @OA\Get(
     *   path="/api/subcategories/{id}",
     *   tags={"Subcategorias"},
     *   summary="Ver una subcategoría",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Subcategory")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Subcategory $subcategory)
    {
        return new SubcategoryResource($subcategory);
    }
}
