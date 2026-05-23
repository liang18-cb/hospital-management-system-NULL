<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\MedicalRecordController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\ReportController;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('token.invalidate');

        Route::middleware('role:admin')->group(function () {
            Route::get('/patients', [PatientController::class, 'index']);
            Route::get('/reports/export', [ReportController::class, 'export']);
            Route::apiResource('doctors', DoctorController::class);
        });

        Route::middleware('role:admin,patient')->group(function () {
            Route::get('/patients/{id}', [PatientController::class, 'show']);
            Route::put('/patients/{id}', [PatientController::class, 'update']);
        });

        Route::get('/doctors', [DoctorController::class, 'index']);

        Route::middleware('role:patient')->group(function () {
            Route::post('/appointments', [AppointmentController::class, 'store']);
        });

        Route::middleware('role:admin,doctor,patient')->group(function () {
            Route::get('/appointments/{id}', [AppointmentController::class, 'show']);
            Route::get('/medical-records/{id}', [MedicalRecordController::class, 'show']);
            Route::get('/files/{id}', [FileController::class, 'show']);
            Route::post('/files/upload', [FileController::class, 'store']);
            Route::put('/files/{id}', [FileController::class, 'update']);
        });

        Route::middleware('role:admin,doctor')->group(function () {
            Route::put('/appointments/{id}', [AppointmentController::class, 'update']);
            Route::post('/medical-records', [MedicalRecordController::class, 'store']);
        });

        Route::middleware('role:admin,patient')->group(function () {
            Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy']);
            Route::delete('/files/{id}', [FileController::class, 'destroy']);
        });
    });
});