<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\MedicalRecordController;
use App\Http\Controllers\API\FileController;

Route::prefix('v1')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/me', function (Request $request) {
            return $request->user();
        });

        Route::get('/doctors', [DoctorController::class, 'index']);
        Route::get('/doctors/{id}', [DoctorController::class, 'show']);
        Route::post('/doctors', [DoctorController::class, 'store']);
        Route::put('/doctors/{id}', [DoctorController::class, 'update']);
        Route::delete('/doctors/{id}', [DoctorController::class, 'destroy']);

        Route::get('/patients', [PatientController::class, 'index']);
        Route::get('/patients/{id}', [PatientController::class, 'show']);
        Route::post('/patients', [PatientController::class, 'store']);
        Route::put('/patients/{id}', [PatientController::class, 'update']);
        Route::delete('/patients/{id}', [PatientController::class, 'destroy']);

        Route::apiResource('appointments', AppointmentController::class);
        Route::apiResource('medical-records', MedicalRecordController::class);
        Route::apiResource('files', FileController::class);

    });
});