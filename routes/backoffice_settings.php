<?php

use App\Src\Infrastructure\Controllers\Backoffice\Settings\PasswordController;
use App\Src\Infrastructure\Controllers\Backoffice\Settings\ProfileController;
use App\Src\Infrastructure\Controllers\Backoffice\Settings\PaymentSettingsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('settings/payments', [PaymentSettingsController::class, 'index'])->name('settings.payments');
Route::put('settings/payments/global', [PaymentSettingsController::class, 'updateGlobal'])->name('settings.payments.global');
Route::put('settings/payments/mediator/{id}', [PaymentSettingsController::class, 'updateMediator'])->name('settings.payments.mediator');
Route::post('settings/payments/stripe/create-account', [\App\Src\Infrastructure\Controllers\Payments\StripeAccountController::class, 'create'])->name('settings.payments.stripe.create');


Route::redirect('settings', '/settings/profile');

Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

Route::get('settings/appearance', function () {
    return Inertia::render('settings/appearance');
})->name('appearance');

Route::get('settings/gateways', [\App\Src\Infrastructure\Controllers\Backoffice\Settings\GatewaySettingsController::class, 'index'])->name('settings.gateways');
Route::put('settings/gateways/{slug}', [\App\Src\Infrastructure\Controllers\Backoffice\Settings\GatewaySettingsController::class, 'update'])->name('settings.gateways.update');
