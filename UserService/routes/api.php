<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// User routes
Route::apiResource('users', UserController::class);

// Get user's order history
Route::get('users/{id}/orders', [UserController::class, 'getUserOrders']); 