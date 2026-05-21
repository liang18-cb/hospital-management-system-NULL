<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\MedicalRecordController;
use App\Http\Controllers\API\FileController;

Route::prefix('v1')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout'])->middleware('token.invalidate');

        Route::get('/me', function (Request $request) {
            return $request->user();
        });

        Route::middleware('verified.email')->group(function () {

            Route::middleware('role:admin')->group(function () {
                Route::apiResource('doctors', DoctorController::class);
                Route::apiResource('patients', PatientController::class);
            });

            Route::middleware('role:admin,doctor,patient')->group(function () {
                Route::get('/appointments', [AppointmentController::class, 'index']);
                Route::get('/appointments/{appointment}', [AppointmentController::class, 'show']);
            });

            Route::middleware('role:patient')->group(function () {
                Route::post('/appointments', [AppointmentController::class, 'store']);
            });

            Route::middleware('role:admin')->group(function () {
                Route::put('/appointments/{appointment}', [AppointmentController::class, 'update']);
                Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy']);
            });

            Route::middleware('role:admin,doctor,patient')->group(function () {
                Route::get('/medical-records', [MedicalRecordController::class, 'index']);
                Route::get('/medical-records/{medical_record}', [MedicalRecordController::class, 'show']);
            });

            Route::middleware('role:doctor')->group(function () {
                Route::post('/medical-records', [MedicalRecordController::class, 'store']);
            });

            Route::middleware('role:admin')->group(function () {
                Route::put('/medical-records/{medical_record}', [MedicalRecordController::class, 'update']);
                Route::delete('/medical-records/{medical_record}', [MedicalRecordController::class, 'destroy']);
            });

            Route::middleware('role:admin')->group(function () {
                Route::get('/files', [FileController::class, 'index']);
                Route::delete('/files/{file}', [FileController::class, 'destroy']);
            });

            Route::middleware('role:doctor,patient')->group(function () {
                Route::post('/files', [FileController::class, 'store']);
                Route::get('/files/{file}', [FileController::class, 'show']);
            });

        });
    });
});