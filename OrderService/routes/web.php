<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', [OrderController::class, 'index'])->name('orders.index');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

// API Routes
Route::prefix('api')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/user/{userId}', [OrderController::class, 'getUserOrders']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
});
