<?php

Route::apiResource('/users', App\Http\Controllers\API\UserController::class);

Route::apiResource('/users', App\Http\Controllers\API\UserController::class);
Route::post('/users/bulk', [App\Http\Controllers\API\UserController::class, 'bulkStore']);
Route::put('/users/bulk', [App\Http\Controllers\API\UserController::class, 'bulkUpdate']);
Route::delete('/users/bulk', [App\Http\Controllers\API\UserController::class, 'bulkDestroy']);
