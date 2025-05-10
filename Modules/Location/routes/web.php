<?php

use Illuminate\Support\Facades\Route;
use Modules\Location\Http\Controllers\LocationController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('location', LocationController::class)->names('location');
});
