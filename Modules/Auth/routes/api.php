<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\RoleController;
use Modules\Auth\Http\Controllers\AuthController;


Route::prefix('roles')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::put('/{role}', [RoleController::class, 'update']);
        Route::delete('/{role}', [RoleController::class, 'destroy']);
    });
});

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

