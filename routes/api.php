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

Route::apiResource('/users', App\Http\Controllers\API\UserController::class)->middleware('auth:sanctum');
Route::apiResource('users', UserController::class)->whereNumber('user');

Route::apiResource('/patients', App\Http\Controllers\API\PatientController::class);

Route::apiResource('/chronic_diseases', App\Http\Controllers\API\ChronicDiseasesController::class)->parameters([
    'chronic_diseases' => 'chronicDisease',
]);

Route::apiResource('/chronic_medications', App\Http\Controllers\API\ChronicMedicationsController::class)->parameters([
    'chronic_medications' => 'chronicMedication',
]);
