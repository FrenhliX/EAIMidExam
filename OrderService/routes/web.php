<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', [OrderController::class, 'index'])->name('orders.index');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');
Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
Route::get('/api/products', [OrderController::class, 'getProducts'])->name('products.get');
Route::get('/api/users', [OrderController::class, 'getUsers'])->name('users.get');

// API Routes
Route::prefix('api')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/user/{userId}', [OrderController::class, 'getUserOrders']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
});
