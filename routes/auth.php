<?php

use Illuminate\Support\Facades\Route;
use App\Src\Infrastructure\Controllers\Backoffice\Auth\AuthenticatedSessionController;
use App\Src\Infrastructure\Controllers\Backoffice\Auth\ConfirmablePasswordController;
use App\Src\Infrastructure\Controllers\Backoffice\Auth\EmailVerificationPromptController;
use App\Src\Infrastructure\Controllers\Backoffice\Auth\VerifyEmailController;
use App\Src\Infrastructure\Controllers\Backoffice\Auth\EmailVerificationNotificationController;
use App\Src\Infrastructure\Controllers\Backoffice\Auth\RegisteredUserController;
use App\Src\Infrastructure\Controllers\Backoffice\Auth\PasswordResetLinkController;
use App\Src\Infrastructure\Controllers\Backoffice\Auth\NewPasswordController;

Route::get('register', [RegisteredUserController::class, 'create'])
    ->name('register');

Route::post('register', [RegisteredUserController::class, 'store']);

Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login');

Route::post('login', [AuthenticatedSessionController::class, 'store']);

Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
    ->name('password.request');

Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
    ->name('password.email');

Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    ->name('password.reset');

Route::post('reset-password', [NewPasswordController::class, 'store'])
    ->name('password.store');

Route::get('verify-email', EmailVerificationPromptController::class)
    ->name('verification.notice');

Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('verification.send');

Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
    ->name('password.confirm');

Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
