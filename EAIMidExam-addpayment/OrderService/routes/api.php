<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Order routes
Route::apiResource('orders', OrderController::class);

// Get user's orders
Route::get('orders/user/{userId}', [OrderController::class, 'getUserOrders']); 