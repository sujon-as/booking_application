<?php

use App\Http\Controllers\Api\AuthController;

Route::prefix('/v1')->group(function () {
    Route::prefix('/service-providers')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('api.sp.register');
        Route::post('/login', [AuthController::class, 'SPLogin'])->name('api.sp.login');
    });
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('api.sp.logout')
        ->middleware('auth:sanctum');
});
