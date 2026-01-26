<?php

use App\Src\Infrastructure\Controllers\Auth\AuthPagesController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthPagesController::class, 'login'])->name('login');
    Route::get('/register', [AuthPagesController::class, 'register'])->name('register');

    Route::get('/forgot-password', [AuthPagesController::class, 'forgotPassword'])->name('password.request');
    Route::get('/reset-password/{token}', [AuthPagesController::class, 'resetPassword'])->name('password.reset');

    Route::post('/login', [AuthPagesController::class, 'postLogin'])->name('post.login');
    Route::post('/register', [AuthPagesController::class, 'postRegister'])->name('post.register');
    // Redirección para NO usar backend/login
    // Redirección para legacy backoffice login
    Route::redirect('/backoffice/login', '/login', 301);
});
