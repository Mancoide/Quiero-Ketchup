<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends ApiController
{
    /**
     * @OA\Get(
     *   path="/api/restaurants",
     *   tags={"Sucursales"},
     *   summary="Listar sucursales",
     *   @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer", default=15)),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Restaurant"))
     *     )
     *   )
     * )
     */
    public function index(Request $request)
    {
        $restaurants = $this->paginate(
            $request,
            Restaurant::query()->orderBy('name'),
        );

        return RestaurantResource::collection($restaurants);
    }

    /**
     * @OA\Get(
     *   path="/api/restaurants/{id}",
     *   tags={"Sucursales"},
     *   summary="Ver una sucursal",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Restaurant")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Restaurant $restaurant)
    {
        return new RestaurantResource($restaurant);
    }
}
