<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\PatientController;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PUBLIC ROUTES
    |--------------------------------------------------------------------------
    */

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);


    /*
    |--------------------------------------------------------------------------
    | PROTECTED ROUTES
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:sanctum')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | AUTH
        |--------------------------------------------------------------------------
        */

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/me', function (Request $request) {
            return $request->user();
        });


        /*
        |--------------------------------------------------------------------------
        | DOCTORS
        |--------------------------------------------------------------------------
        */

        Route::get('/doctors', [DoctorController::class, 'index']);
        Route::get('/doctors/{id}', [DoctorController::class, 'show']);
        Route::post('/doctors', [DoctorController::class, 'store']);
        Route::put('/doctors/{id}', [DoctorController::class, 'update']);
        Route::delete('/doctors/{id}', [DoctorController::class, 'destroy']);


        /*
        |--------------------------------------------------------------------------
        | PATIENTS
        |--------------------------------------------------------------------------
        */

        Route::get('/patients', [PatientController::class, 'index']);
        Route::get('/patients/{id}', [PatientController::class, 'show']);
        Route::post('/patients', [PatientController::class, 'store']);
        Route::put('/patients/{id}', [PatientController::class, 'update']);
        Route::delete('/patients/{id}', [PatientController::class, 'destroy']);

    });
});