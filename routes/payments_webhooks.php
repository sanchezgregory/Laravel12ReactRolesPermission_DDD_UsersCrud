<?php

use App\Src\Infrastructure\Controllers\Payments\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/payments/{method}/webhook', PaymentWebhookController::class)->name('api.payments.webhook');
