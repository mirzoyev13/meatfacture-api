<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Получить историю заказов авторизованного пользователя",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Список заказов",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=7),
     *                 @OA\Property(property="total", type="number", format="float", example=45.00),
     *                 @OA\Property(property="comment", type="string", example="test comment"),
     *                 @OA\Property(property="status", type="string", example="done"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-17T23:09:06.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-17T23:09:06.000000Z"),
     *                 @OA\Property(property="items", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="order_id", type="integer", example=1),
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=2),
     *                     @OA\Property(property="price", type="number", format="float", example=22.50),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-17T23:09:06.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-17T23:09:06.000000Z"),
     *                     @OA\Property(property="product", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Steak"),
     *                         @OA\Property(property="description", type="string", example="Rare, medium, well done"),
     *                         @OA\Property(property="price", type="number", format="float", example=22.50),
     *                         @OA\Property(property="category", type="string", example="Beef"),
     *                         @OA\Property(property="in_stock", type="boolean", example=true),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-16T23:13:34.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-16T23:13:34.000000Z")
     *                     )
     *                 ))
     *             ))
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()
            ->with(['items.product'])
            ->latest()
            ->get();

        return response()->json([
            'data' => $orders
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Оформить заказ",
     *     tags={"Orders"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items"},
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *     @OA\Property(property="quantity", type="integer", example=2, maximum=10, minimum=1)
     *                 )
     *             ),
     *             @OA\Property(property="comment", type="string", example="Swagger тестовый коммент")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Заказ успешно создан",
     *         @OA\JsonContent(
     *             @OA\Property(property="order_id", type="integer", example=5),
     *             @OA\Property(property="status", type="string", example="created"),
     *             @OA\Property(property="total", type="number", format="float", example=5000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Ошибка при создании заказа"
     *     )
     * )
     */
    public function store(OrderStoreRequest $request): JsonResponse
    {
        try {
            $order = $this->orderService->createOrder(auth('api')->user(), $request->validated());

            return response()->json([
                'order_id' => $order->id,
                'status'   => 'created',
                'total'    => $order->total,
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'error'   => 'Catch an exception'], 500);
        }
    }
}
