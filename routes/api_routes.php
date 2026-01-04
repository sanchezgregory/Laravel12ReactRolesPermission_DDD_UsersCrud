<?php

use Illuminate\Support\Facades\Route;
use App\Src\Infrastructure\Controllers\Api; // Namespace para los controladores de API


Route::post('/logout', [Api\AuthController::class, 'logout'])->name('logout');
Route::post('/refresh', [Api\AuthController::class, 'refresh'])->name('refresh');
Route::get('/me', [Api\AuthController::class, 'me'])->name('me');

require __DIR__ . '/payments_webhooks.php';
