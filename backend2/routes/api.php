<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\VendingController;
use Illuminate\Session\Middleware\StartSession;

// Public API routes without session (เช่น product, cash)
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{product}', [ProductController::class, 'update']);
Route::delete('/products/{product}', [ProductController::class, 'destroy']);

Route::get('/cash', [CashController::class, 'index']);
Route::post('/cash', [CashController::class, 'store']);
Route::put('/cash/{cashUnit}', [CashController::class, 'update']);
Route::delete('/cash/{cashUnit}', [CashController::class, 'destroy']);

// Group routes ที่ต้องใช้ session (เช่น insert-coin, purchase, status)
Route::middleware([StartSession::class])->group(function () {
    Route::get('/status', [VendingController::class, 'status']);
    Route::get('/change-options', [VendingController::class, 'changeOptions']);
    Route::post('/insert-coin', [VendingController::class, 'insertCoin']);
    Route::post('/purchase', [VendingController::class, 'purchase']);
});

// Authentication route (ใช้ sanctum middleware)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
