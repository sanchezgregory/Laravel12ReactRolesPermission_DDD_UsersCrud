<?php

use App\Src\Infrastructure\Controllers\Payments\CreateCheckoutSessionController;
use App\Src\Infrastructure\Controllers\Payments\StripeCheckPaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('payments')->name('payments.')->group(function () {
    Route::post('/checkout', CreateCheckoutSessionController::class)->name('checkout');

    // Estas 2 son solo “landing” (NO confirman pago)
    Route::post('/submit-schedule', \App\Src\Infrastructure\Controllers\Payments\SubmitScheduledSessionController::class)->name('submit-schedule');
    Route::get('/cancel', StripeCheckPaymentController::class)->name('cancel');
});
