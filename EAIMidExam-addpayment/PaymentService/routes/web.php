<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

// Redirect root to payments index
Route::get('/', function () {
    return redirect()->route('payments.index');
});

Route::get('/payments', [PaymentController::class, 'webIndex'])->name('payments.index');
Route::get('/payments/create', [PaymentController::class, 'webCreate'])->name('payments.create');
Route::post('/payments', [PaymentController::class, 'webStore'])->name('payments.store');
Route::get('/payments/{id}', [PaymentController::class, 'webShow'])->name('payments.show');
Route::get('/payments/{id}/edit', [PaymentController::class, 'webEdit'])->name('payments.edit');
Route::put('/payments/{id}', [PaymentController::class, 'webUpdate'])->name('payments.update');
Route::delete('/payments/{id}', [PaymentController::class, 'webDestroy'])->name('payments.destroy');
