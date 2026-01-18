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
Route::group(['middleware' => ['verified']], function () {

    // ADMIN ONLY ROUTES
    Route::middleware(['role:admin'])->group(function () {
        require __DIR__ . '/backoffice_settings.php';
        
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

    // SHARED OR MEDIATOR ROUTES will go here
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');



    Route::middleware(['role:mediator'])->group(function () {
        Route::get('/my-clients', \App\Src\Infrastructure\Controllers\Backoffice\MediatorSpace\ClientsController::class)->name('mediator.clients');
        Route::get('/my-payments', \App\Src\Infrastructure\Controllers\Backoffice\MediatorSpace\PaymentsController::class)->name('mediator.payments');
        Route::get('/my-sessions', \App\Src\Infrastructure\Controllers\Backoffice\MediatorSpace\SessionsController::class)->name('mediator.sessions');
        Route::post('/my-sessions/{sessionId}/confirm', \App\Src\Infrastructure\Controllers\Backoffice\MediatorSpace\ConfirmSessionController::class)->name('mediator.sessions.confirm');
        Route::post('/my-sessions/update-schedule', \App\Src\Infrastructure\Controllers\Backoffice\MediatorSpace\UpdateSessionScheduleController::class)->name('mediator.sessions.update-schedule');
    });

});
