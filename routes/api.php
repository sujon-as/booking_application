<?php

use App\Http\Controllers\Api\AuthController;

Route::prefix('/v1')->group(function () {
    Route::prefix('/service-providers')->group(function () {
        Route::post('/test', function () {
            return response()->json(['ok' => true]);
        });
        Route::post('/register', [AuthController::class, 'register'])->name('api.sp.register');
    });

});
