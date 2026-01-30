<?php

use Illuminate\Support\Facades\Route;

Route::get('/my-sessions', \App\Src\Infrastructure\Controllers\Backoffice\UserSpace\MySessionsController::class)->name('user.sessions');
Route::get('/my-coupons', \App\Src\Infrastructure\Controllers\Backoffice\UserSpace\MyCouponsController::class)->name('user.coupons');
Route::post('/coupons/validate', \App\Src\Infrastructure\Controllers\Coupons\ValidateCouponController::class)->name('coupons.validate');
