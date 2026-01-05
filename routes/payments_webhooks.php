<?php

use App\Src\Infrastructure\Controllers\Payments\StripePaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/payments/stripe/webhook', StripePaymentWebhookController::class)->name('api.payments.webhook');
