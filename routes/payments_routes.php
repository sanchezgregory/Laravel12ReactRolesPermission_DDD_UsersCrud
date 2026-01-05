<?php

use App\Src\Infrastructure\Controllers\Payments\CreateCheckoutSessionController;
use Illuminate\Support\Facades\Route;

Route::prefix('payments')->name('payments.')->group(function () {
    Route::post('/checkout', CreateCheckoutSessionController::class)->name('checkout');

    // Estas 2 son solo “landing” (NO confirman pago)
    Route::get('/success', fn () => inertia('payments/Success'))->name('success');
    Route::get('/cancel', fn () => inertia('payments/Cancel'))->name('cancel');
});
