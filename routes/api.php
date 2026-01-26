<?php

declare(strict_types=1);

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ChronicDiseasesController;
use App\Http\Controllers\API\ChronicMedicationsController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/forget-password', [AuthController::class, 'forgotPassword']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('/users', UserController::class);

    Route::apiResource('/patients', PatientController::class);

    Route::apiResource('/chronic_diseases', ChronicDiseasesController::class);

    Route::apiResource('/chronic_medications', ChronicMedicationsController::class);
});

