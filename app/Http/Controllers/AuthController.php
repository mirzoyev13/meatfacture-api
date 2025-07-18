<?php

namespace App\Http\Controllers;
/**
 * @OA\SecurityScheme(
 *     type="http",
 *     description="JWT Bearer",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
 * )
 */
/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Masofaktura API",
 *     description="Документация для авторизации и заказов"
 * )
 */
use OpenApi\Annotations as OA;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Регистрация нового пользователя",
     *     tags={"Login and Register"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "phone", "email", "address", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Алейскй"),
     *             @OA\Property(property="phone", type="string", example="+994501112239"),
     *             @OA\Property(property="email", type="string", example="lewa123@example.com"),
     *             @OA\Property(property="address", type="string", example="г. Москва, улица 1"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Успешная регистрация",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successful authentification"),
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации"
     *     )
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->respondWithToken(auth('api')->login($user), 'Successful authentification', 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Вход пользователя по номеру телефона и паролю",
     *     tags={"Login and Register"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone", "password"},
     *             @OA\Property(property="phone", type="string", example="+994501112233"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный вход",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successful login"),
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="token_type", type="string", example="bearer"),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неверный номер или пароль"
     *     )
     * )
     */

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only(['phone', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Incorrect phone or password',
            ], 401);
        }

        return $this->respondWithToken($token, 'Successful login');
    }

    protected function respondWithToken(string $token, string $message, int $status = 200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60, // для двтшки
        ], $status);
    }
}
