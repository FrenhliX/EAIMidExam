<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;


// User Management Routes
Route::get('/', [UserController::class, 'index'])->name('users.index');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit'); // <-- edit

// API Routes
Route::prefix('api')->group(function () {
    Route::get('/users', [UserController::class, 'apiIndex']);
    Route::get('/users/{id}', [UserController::class, 'apiShow']);
    Route::apiResource('products', ProductController::class);
    Route::get('/users/{userId}/orders', [UserController::class, 'getUserOrders']);
});