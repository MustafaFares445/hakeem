<?php

declare(strict_types=1);

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BillingController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\ChronicDiseasesController;
use App\Http\Controllers\API\ChronicMedicationsController;
use App\Http\Controllers\API\ClinicController;
use App\Http\Controllers\API\DentalLabController;
use App\Http\Controllers\API\FillerMaterialController;
use App\Http\Controllers\API\MediaController;
use App\Http\Controllers\API\MedicalRecordController;
use App\Http\Controllers\API\MedicalRecordTreatmentController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\TreatmentController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::post('/forget-password', [AuthController::class, 'forgotPassword']);
    Route::put('/update-info', [AuthController::class, 'updateInfo'])->middleware('auth:sanctum');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('/users', UserController::class);

    Route::apiResource('/patients', PatientController::class);
    Route::get('/patients/{patient}/tooth-overview', [PatientController::class, 'toothOverview']);

    Route::apiResource('/chronic_diseases', ChronicDiseasesController::class);

    Route::apiResource('/chronic_medications', ChronicMedicationsController::class);

    Route::apiResource('/bookings', BookingController::class);

    Route::apiResource('/billings', BillingController::class);

    Route::apiResource('/dental-labs', DentalLabController::class);

    Route::apiResource('/treatments', TreatmentController::class);

    Route::apiResource('/filler-materials', FillerMaterialController::class);

    Route::apiResource('/medical-records', MedicalRecordController::class);

    Route::apiResource('/medical-record-treatments', MedicalRecordTreatmentController::class);

    Route::apiResource('media', MediaController::class)->only(['index', 'store', 'show', 'destroy']);

    Route::get('/clinic', [ClinicController::class, 'show']);
    Route::put('/clinic', [ClinicController::class, 'update']);
});
