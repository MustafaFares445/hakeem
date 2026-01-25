<?php

declare(strict_types=1);

use App\Http\Controllers\API\UserBulkController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->group(function (): void {
    Route::post('bulk', [UserBulkController::class, 'store']);
    Route::put('bulk', [UserBulkController::class, 'update']);
    Route::delete('bulk', [UserBulkController::class, 'destroy']);
});

Route::apiResource('users', UserController::class)->whereNumber('user');

Route::apiResource('/patients', App\Http\Controllers\API\PatientController::class);

Route::apiResource('/chronic_diseases', App\Http\Controllers\API\ChronicDiseasesController::class)->parameters([
    'chronic_diseases' => 'chronicDisease',
]);
