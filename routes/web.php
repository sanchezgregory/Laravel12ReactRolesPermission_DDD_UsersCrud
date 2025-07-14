<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

// // Rutas de Backoffice (solo para administradores)
// Route::middleware('auth', 'role:admin')->group(function () {
//     require __DIR__ . '/admin_routes.php';
// });

// // Rutas de Frontoffice (solo para usuarios)
// Route::middleware('auth', 'role:user')->group(function () {
//     require __DIR__ . '/user_routes.php';
// });
