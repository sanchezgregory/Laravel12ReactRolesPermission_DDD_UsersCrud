<?php

use App\Src\Infrastructure\Controllers\Payments\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/payments/stripe', [PaymentWebhookController::class, 'handleStripe'])->name('webhooks.payments.stripe');
Route::post('/webhooks/payments/mercadopago', [PaymentWebhookController::class, 'handleMercadoPago'])->name('webhooks.payments.mercadopago');
