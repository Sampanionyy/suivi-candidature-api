<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\ApplicationStatsController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
});

Route::middleware('auth:sanctum')->group(function () {
    
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });

    Route::apiResource('applications', ApplicationController::class);
    
    Route::prefix('applications/{application}')->group(function () {
        Route::post('upload-cv', [ApplicationController::class, 'uploadCV']);
        Route::post('upload-cover-letter', [ApplicationController::class, 'uploadCoverLetter']);
        Route::patch('status', [ApplicationController::class, 'updateStatus']);
    });

    Route::get('applications-stats', [ApplicationStatsController::class, 'index']);
    Route::get('applications-interviews', [ApplicationController::class, 'interviews']);
});
