<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends ApiController
{
    /**
     * @OA\Get(
     *   path="/api/products",
     *   tags={"Productos"},
     *   summary="Listar productos",
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=15)),
    *   @OA\Parameter(name="category_id", in="query", required=false, @OA\Schema(type="integer")),
    *   @OA\Parameter(name="subcategory_id", in="query", required=false, @OA\Schema(type="integer")),
    *   @OA\Parameter(name="restaurant_id", in="query", required=false, @OA\Schema(type="integer")),
    *   @OA\Parameter(name="in_promotion", in="query", required=false, @OA\Schema(type="boolean")),
    *   @OA\Parameter(name="available", in="query", required=false, @OA\Schema(type="boolean")),
    *   @OA\Parameter(name="search", in="query", required=false, @OA\Schema(type="string")),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     )
     *   )
     * )
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('category_id')) {
            $query->where('category_id', (int) $request->input('category_id'));
        }

        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', (int) $request->input('subcategory_id'));
        }

        if ($request->filled('restaurant_id')) {
            $restaurantId = (int) $request->input('restaurant_id');

            $query->whereHas('restaurants', fn ($q) => $q->whereKey($restaurantId));
        }

        if ($request->has('in_promotion')) {
            $inPromotion = filter_var($request->input('in_promotion'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if ($inPromotion === true) {
                $query->whereHas('promotions');
            } elseif ($inPromotion === false) {
                $query->whereDoesntHave('promotions');
            }
        }

        if ($request->has('available')) {
            $available = filter_var($request->input('available'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if ($available !== null) {
                $query->where('available', $available);
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $products = $this->paginate(
            $request,
            $query
                ->with([
                    'category',
                    'subcategory',
                    'restaurants',
                    'options.items',
                    'promotions',
                ])
                ->orderBy('name')
        );

        return ProductResource::collection($products);
    }

    /**
     * @OA\Get(
     *   path="/api/products/{id}",
     *   tags={"Productos"},
     *   summary="Ver un producto",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Product")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Product $product)
    {
        $product->load([
            'category',
            'subcategory',
            'restaurants',
            'options.items',
            'promotions',
        ]);

        return new ProductResource($product);
    }
}
