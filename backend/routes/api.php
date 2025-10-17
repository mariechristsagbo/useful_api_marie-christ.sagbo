<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
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

Route::middleware(['auth:sanctum', 'CheckModuleActive'])->group(function () {
    Route::post('/shorten', [LinkController::class, 'shorten']);
    Route::get('/links', [LinkController::class, 'index']);
    Route::delete('/links/{id}', [LinkController::class, 'destroy']);
});

Route::get('/s/{code}', [LinkController::class, 'redirect']);

Route::middleware(['auth:sanctum', 'CheckModuleActive'])->group(function () {
    Route::get('/wallet', [WalletController::class, 'balance']);
    Route::post('/wallet/transfer', [WalletController::class, 'transfer']);
    Route::post('/wallet/topup', [WalletController::class, 'topup']);
    Route::get('/wallet/transactions', [WalletController::class, 'transactions']);
});

Route::middleware(['auth:sanctum', 'CheckModuleActive:Marketplace + Stock Manager'])->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products/{id}/restock', [ProductController::class, 'restock']);
    Route::post('/orders', [OrderController::class, 'store']);
});
