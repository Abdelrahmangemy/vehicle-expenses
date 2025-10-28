<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VehicleExpenseController;
use App\Http\Middleware\CustomThrottle;

Route::middleware([CustomThrottle::class])->group(function () {
    Route::get('/vehicle-expenses', [VehicleExpenseController::class, 'index']);
});
