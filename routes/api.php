<?php

declare(strict_types=1);

use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/forget-password', [AuthController::class, 'forgotPassword']);
});

Route::apiResource('/users', App\Http\Controllers\API\UserController::class);
