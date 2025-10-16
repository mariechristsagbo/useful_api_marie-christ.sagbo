<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('modules')->group(function () {
        Route::get('/', [ModuleController::class, 'index']); 
        Route::post('/{moduleId}/activate', [ModuleController::class, 'activate']); 
        Route::post('/{moduleId}/deactivate', [ModuleController::class, 'deactivate']); 
    });
});

