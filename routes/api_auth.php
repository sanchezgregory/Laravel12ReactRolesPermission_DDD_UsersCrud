<?php

use Illuminate\Support\Facades\Route;
use App\Src\Infrastructure\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
