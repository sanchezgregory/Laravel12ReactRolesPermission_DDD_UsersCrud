<?php

use Illuminate\Support\Facades\Route;

Route::get('/my-sessions', \App\Src\Infrastructure\Controllers\Backoffice\UserSpace\MySessionsController::class)->name('user.sessions');
