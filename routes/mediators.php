<?php

use App\Src\Infrastructure\Controllers\Mediators;
use Illuminate\Support\Facades\Route;

Route::prefix('mediators')->name('mediators.')->group(function () {
    Route::get('/', Mediators\IndexController::class)->name('index');
    Route::get('/{id}', Mediators\ShowController::class)->name('show');
});
