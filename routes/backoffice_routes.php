<?php

namespace App\Http\Routes;

use App\Src\Infrastructure\Controllers\Backoffice\Users\IndexController;
use App\Src\Infrastructure\Controllers\Backoffice\Users\CreateController;
use App\Src\Infrastructure\Controllers\Backoffice\Users\StoreController;
use App\Src\Infrastructure\Controllers\Backoffice\Users\EditController;
use App\Src\Infrastructure\Controllers\Backoffice\Users\UpdateController;
use App\Src\Infrastructure\Controllers\Backoffice\Users\DestroyController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Protected routes (require authentication and admin role)
Route::middleware(['verified', 'role:admin'])->group(function () {
    require __DIR__ . '/backoffice_settings.php';

    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Rutas para el CRUD de Usuarios
    Route::get('/users', IndexController::class)->name('users.index');
    Route::get('/users/create', CreateController::class)->name('users.create');
    Route::post('/users', StoreController::class)->name('users.store');
    Route::get('/users/{id}/edit', EditController::class)->name('users.edit');
    Route::put('/users/{id}', UpdateController::class)->name('users.update');
    Route::delete('/users/{id}', DestroyController::class)->name('users.destroy');

    // Rutas para el CRUD de Mediadores
    Route::get('/mediators', \App\Src\Infrastructure\Controllers\Backoffice\Mediators\IndexController::class)->name('mediators.index');
    Route::get('/mediators/create', \App\Src\Infrastructure\Controllers\Backoffice\Mediators\CreateController::class)->name('mediators.create');
    Route::post('/mediators', \App\Src\Infrastructure\Controllers\Backoffice\Mediators\StoreController::class)->name('mediators.store');
    Route::get('/mediators/{id}/edit', \App\Src\Infrastructure\Controllers\Backoffice\Mediators\EditController::class)->name('mediators.edit');
    Route::put('/mediators/{id}', \App\Src\Infrastructure\Controllers\Backoffice\Mediators\UpdateController::class)->name('mediators.update');
    Route::delete('/mediators/{id}', \App\Src\Infrastructure\Controllers\Backoffice\Mediators\DestroyController::class)->name('mediators.destroy');
});
