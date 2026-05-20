<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    /**
     * POST /api/register
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());

            return ApiResponse::created([
                'user'  => new UserResource($result['user']),
                'token' => $result['token'],
            ], 'Registrasi berhasil.');
        } catch (\Exception $e) {
            return ApiResponse::error('Registrasi gagal: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());

            return ApiResponse::success([
                'user'  => new UserResource($result['user']),
                'token' => $result['token'],
            ], 'Login berhasil.');
        } catch (ValidationException $e) {
            return ApiResponse::error($e->getMessage(), 401);
        } catch (\Exception $e) {
            return ApiResponse::error('Login gagal.', 500);
        }
    }

    /**
     * POST /api/logout  (requires auth:sanctum)
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return ApiResponse::success(null, 'Logout berhasil.');
    }

    /**
     * GET /api/me  (requires auth:sanctum)
     */
    public function me(Request $request): JsonResponse
    {
        $user = $this->authService->profile($request->user());

        return ApiResponse::success(new UserResource($user), 'Profile berhasil diambil.');
    }
}
