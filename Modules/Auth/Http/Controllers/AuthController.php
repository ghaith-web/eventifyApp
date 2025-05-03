<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Auth\Services\AuthService;
use Modules\Auth\Models\User;
use Modules\Auth\Http\Requests\AuthRequest;
use Modules\Auth\Transformers\UserResource;
use App\Helpers\ApiResponseHelper;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(AuthRequest $request)
    {
        try {
            $user = $this->authService->register($request->validated());
            $user->load('role');

            return ApiResponseHelper::success(
                new UserResource($user),
                'Registered successfully',
                201
            );
        } catch (\Throwable $e) {
            return ApiResponseHelper::error('Registration failed', 500, null, $e);
        }
    }

    public function login(Request $request)
    {
        try {
            $token = $this->authService->login($request->only(['email', 'password']));

            if (!$token) {
                return ApiResponseHelper::error('Invalid credentials', 401);
            }

            return ApiResponseHelper::success([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ], 'Login successful');
        } catch (\Throwable $e) {
            return ApiResponseHelper::error('Login failed', 500, null, $e);
        }
    }

    public function logout()
    {
        try {
            $this->authService->logout();
            return ApiResponseHelper::success(null, 'Logged out successfully');
        } catch (\Throwable $e) {
            return ApiResponseHelper::error('Logout failed', 500, null, $e);
        }
    }

    public function profile()
    {
        try {
            $user = $this->authService->profile();
            $user->load('role');

            return ApiResponseHelper::success(new UserResource($user), 'Profile fetched');
        } catch (\Throwable $e) {
            return ApiResponseHelper::error('Failed to fetch profile', 500, null, $e);
        }
    }

    public function refresh()
    {
        try {
            return ApiResponseHelper::success([
                'access_token' => $this->authService->refresh(),
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ], 'Token refreshed');
        } catch (\Throwable $e) {
            return ApiResponseHelper::error('Token refresh failed', 500, null, $e);
        }
    }
}
