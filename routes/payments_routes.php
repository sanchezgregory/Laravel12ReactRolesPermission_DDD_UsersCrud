<?php

use App\Src\Infrastructure\Controllers\Payments\CreateCheckoutSessionController;
use App\Src\Infrastructure\Controllers\Payments\PaymentReturnController;
use Illuminate\Support\Facades\Route;

Route::prefix('payments')->name('payments.')->group(function () {
    Route::post('/checkout', CreateCheckoutSessionController::class)->name('checkout')->middleware('auth:sanctum');

    // Payment return URLs - Gateway agnostic (works for Stripe, MercadoPago, etc.)
    Route::post('/submit-schedule', \App\Src\Infrastructure\Controllers\Payments\SubmitScheduledSessionController::class)->name('submit-schedule')->middleware('auth:sanctum');
    Route::get('/success', PaymentReturnController::class)->name('success');
    Route::get('/cancel', PaymentReturnController::class)->name('cancel');
    Route::get('/pending', PaymentReturnController::class)->name('pending');
});
