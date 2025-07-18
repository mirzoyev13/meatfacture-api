<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Получить список всех товаров",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ со списком товаров",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Стейк"),
     *                 @OA\Property(property="description", type="string", example="Стейк средней выдержки"),
     *                 @OA\Property(property="price", type="number", format="float", example=4500),
     *                 @OA\Property(property="category", type="string", example="Говядина"),
     *                 @OA\Property(property="in_stock", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизованный доступ"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $products = Product::select('id', 'name', 'description', 'price', 'category', 'in_stock')
            ->orderBy('name')
            ->get();

        return response()->json($products, 200);
    }
}
