<?php

use Illuminate\Support\Facades\Route;
use Modules\Location\Http\Controllers\LocationController;



Route::middleware(['auth:api'])->group(function () {
    Route::get('countries', [LocationController::class, 'countries']);
    Route::get('cities/{country}', [LocationController::class, 'cities']);
});