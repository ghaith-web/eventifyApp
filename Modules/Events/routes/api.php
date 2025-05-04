<?php

use Illuminate\Support\Facades\Route;
use Modules\Events\Http\Controllers\CategoryController;

Route::middleware('auth:api')->prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/{category}', [CategoryController::class, 'update']);
    Route::delete('/{category}', [CategoryController::class, 'destroy']);
});