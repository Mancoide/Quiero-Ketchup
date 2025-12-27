<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserStatus;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use App\Models\Product;
use App\Models\ProductOptionItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OrderController extends ApiController
{
    /**
     * @OA\Get(
     *   path="/api/orders",
     *   tags={"Pedidos"},
     *   summary="Obtener mis pedidos",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(
     *     name="per_page",
     *     in="query",
     *     required=false,
     *     @OA\Schema(type="integer", example=15)
     *   ),
     *   @OA\Parameter(
     *     name="page",
     *     in="query",
     *     required=false,
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/Order")
     *       ),
     *       @OA\Property(property="links", type="object"),
     *       @OA\Property(property="meta", type="object")
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($user->status !== UserStatus::ACTIVE || ! $user->hasRole('cliente')) {
            return response()->json(['message' => 'Usuario no autorizado como cliente.'], 403);
        }

        $query = Order::query()
            ->where('user_id', $user->id)
            ->with([
                'restaurant',
                'items.product',
                'items.options',
            ])
            ->orderByDesc('id');

        return OrderResource::collection($this->paginate($request, $query));
    }

    /**
     * @OA\Get(
     *   path="/api/orders/{order}",
     *   tags={"Pedidos"},
     *   summary="Obtener detalle de un pedido mío",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(
     *     name="order",
     *     in="path",
     *     required=true,
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/Order")
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($user->status !== UserStatus::ACTIVE || ! $user->hasRole('cliente')) {
            return response()->json(['message' => 'Usuario no autorizado como cliente.'], 403);
        }

        if ((int) $order->user_id !== (int) $user->id) {
            return response()->json(['message' => 'No tienes acceso a este pedido.'], 403);
        }

        $order->load([
            'restaurant',
            'items.product',
            'items.options',
        ]);

        return new OrderResource($order);
    }

    /**
     * @OA\Post(
     *   path="/api/orders",
     *   tags={"Pedidos"},
        *   summary="Crear un pedido (requiere login)",
        *   description="Requiere token Sanctum (usuario logueado con rol cliente y estado active). Campos obligatorios: restaurant_id, items[].product_id, items[].quantity. Campos opcionales: currency (default PYG si no se envía), metadata, items[].meta, items[].options y options[].meta. En options[] se puede enviar product_option_item_id (recomendado) o bien una opción custom con name y price; esto es posible porque product_option_item_id es nullable en la migración.",
     *   security={{"sanctum":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"restaurant_id","items"},
     *       @OA\Property(property="restaurant_id", type="integer", example=1),
        *       @OA\Property(property="currency", type="string", example="PYG", nullable=true, description="Opcional. Si no se envía se usa PYG (por migración) o se infiere si todos los productos comparten currency."),
        *       @OA\Property(property="metadata", type="object", nullable=true, description="Opcional"),
     *       @OA\Property(
     *         property="items",
     *         type="array",
     *         minItems=1,
     *         @OA\Items(
     *           type="object",
     *           required={"product_id","quantity"},
     *           @OA\Property(property="product_id", type="integer", example=10),
     *           @OA\Property(property="quantity", type="integer", example=2),
        *           @OA\Property(property="meta", type="object", nullable=true, description="Opcional"),
     *           @OA\Property(
     *             property="options",
     *             type="array",
     *             @OA\Items(
     *               type="object",
        *               description="Opcional. Enviar product_option_item_id (recomendado) o name/price si es una opción custom.",
        *               @OA\Property(property="product_option_item_id", type="integer", nullable=true, example=5),
        *               @OA\Property(property="name", type="string", nullable=true, example="Extra queso"),
        *               @OA\Property(property="price", type="number", format="float", nullable=true, example=1000),
        *               @OA\Property(property="meta", type="object", nullable=true)
     *             )
     *           )
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Creado",
     *     @OA\JsonContent(ref="#/components/schemas/Order")
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($user->status !== UserStatus::ACTIVE || ! $user->hasRole('cliente')) {
            return response()->json(['message' => 'Usuario no autorizado como cliente.'], 403);
        }

        $validated = $request->validate([
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,id'],
            'currency' => ['nullable', 'string', 'size:3'],
            'metadata' => ['nullable', 'array'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.meta' => ['nullable', 'array'],
            'items.*.options' => ['nullable', 'array'],
            // En migración product_option_item_id es nullable: se permite opción custom con name/price.
            'items.*.options.*.product_option_item_id' => ['nullable', 'integer', 'exists:product_option_items,id'],
            'items.*.options.*.name' => ['nullable', 'string', 'max:255'],
            'items.*.options.*.price' => ['nullable', 'numeric', 'min:0'],
            'items.*.options.*.meta' => ['nullable', 'array'],
        ]);

        // Validación extra: cada opción debe tener product_option_item_id o name.
        validator($validated, [
            'items.*.options.*' => [
                function ($attribute, $value, $fail) {
                    if (! is_array($value)) {
                        return;
                    }

                    $hasId = isset($value['product_option_item_id']) && $value['product_option_item_id'] !== null;
                    $hasName = isset($value['name']) && is_string($value['name']) && trim($value['name']) !== '';

                    if (! $hasId && ! $hasName) {
                        $fail('Cada opción debe incluir product_option_item_id o name.');
                    }
                },
            ],
        ])->validate();

        $restaurant = Restaurant::query()->findOrFail($validated['restaurant_id']);

        $items = $validated['items'];
        $productIds = collect($items)->pluck('product_id')->unique()->values();

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        // Verifica que todos los productos estén disponibles y pertenezcan a la sucursal.
        $allowedProductIds = $restaurant->products()
            ->whereIn('products.id', $productIds)
            ->pluck('products.id')
            ->values();

        $missingFromRestaurant = $productIds->diff($allowedProductIds);
        if ($missingFromRestaurant->isNotEmpty()) {
            return response()->json([
                'message' => 'Algunos productos no están disponibles en la sucursal seleccionada.',
                'errors' => [
                    'items' => ['Productos fuera de la sucursal: ' . $missingFromRestaurant->implode(', ')],
                ],
            ], 422);
        }

        foreach ($productIds as $productId) {
            $product = $products->get($productId);

            if (! $product) {
                return response()->json(['message' => 'Producto no encontrado.'], 422);
            }

            if (! $product->available) {
                return response()->json([
                    'message' => 'Hay productos no disponibles.',
                    'errors' => [
                        'items' => ["Producto {$productId} no disponible."],
                    ],
                ], 422);
            }
        }

        $allOptionItemIds = collect($items)
            ->flatMap(fn ($item) => collect($item['options'] ?? [])->pluck('product_option_item_id'))
            ->filter()
            ->unique()
            ->values();

        $optionItems = $allOptionItemIds->isEmpty()
            ? collect()
            : ProductOptionItem::query()
                ->with('option')
                ->whereIn('id', $allOptionItemIds)
                ->get()
                ->keyBy('id');

        $currency = isset($validated['currency']) && is_string($validated['currency'])
            ? strtoupper($validated['currency'])
            : null;

        if (! $currency) {
            $productCurrencies = $products->pluck('currency')->filter()->unique()->values();
            $currency = $productCurrencies->count() === 1 ? (string) $productCurrencies->first() : 'PYG';
        }

        $order = DB::transaction(function () use ($user, $restaurant, $items, $products, $optionItems, $currency, $validated) {
            $order = Order::create([
                'user_id' => $user->id,
                'restaurant_id' => $restaurant->id,
                'status' => 'pending',
                'total_amount' => 0,
                'currency' => $currency,
                'metadata' => $validated['metadata'] ?? null,
            ]);

            $totalAmount = 0;

            foreach ($items as $item) {
                $product = $products->get($item['product_id']);
                $quantity = (int) $item['quantity'];

                $unitPrice = (float) $product->price;

                $rawOptions = collect($item['options'] ?? [])->filter(fn ($row) => is_array($row))->values();

                $optionsTotalPerUnit = 0.0;

                // Normaliza opciones para (a) items por id (b) custom name/price.
                $normalizedOptions = $rawOptions->map(function (array $row) use ($product, $optionItems) {
                    $optionItemId = Arr::get($row, 'product_option_item_id');

                    if ($optionItemId) {
                        $optionItem = $optionItems->get($optionItemId);

                        if (! $optionItem || ! $optionItem->option) {
                            abort(422, 'Opción inválida.');
                        }

                        if ((int) $optionItem->option->product_id !== (int) $product->id) {
                            abort(422, 'Hay opciones que no pertenecen al producto.');
                        }

                        return [
                            'product_option_item_id' => (int) $optionItem->id,
                            'name' => (string) $optionItem->name,
                            'price' => (float) $optionItem->price,
                            'meta' => Arr::get($row, 'meta'),
                        ];
                    }

                    $name = (string) Arr::get($row, 'name', '');
                    $price = Arr::get($row, 'price', 0);

                    return [
                        'product_option_item_id' => null,
                        'name' => trim($name),
                        'price' => (float) $price,
                        'meta' => Arr::get($row, 'meta'),
                    ];
                });

                foreach ($normalizedOptions as $opt) {
                    $optionsTotalPerUnit += (float) $opt['price'];
                }

                $totalPrice = ($unitPrice + $optionsTotalPerUnit) * $quantity;

                /** @var OrderItem $orderItem */
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'meta' => $item['meta'] ?? null,
                ]);

                foreach ($normalizedOptions as $opt) {
                    OrderItemOption::create([
                        'order_item_id' => $orderItem->id,
                        'product_option_item_id' => $opt['product_option_item_id'],
                        'name' => $opt['name'],
                        'price' => (float) $opt['price'],
                        'meta' => $opt['meta'] ?? null,
                    ]);
                }

                $totalAmount += $totalPrice;
            }

            $order->total_amount = $totalAmount;
            $order->save();

            return $order;
        });

        $order->load([
            'restaurant',
            'items.product',
            'items.options',
        ]);

        return response()->json(new OrderResource($order), 201);
    }
}
