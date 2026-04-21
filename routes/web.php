<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPageController::class)->name('landing');
Route::get('/success', [OrderController::class, 'success'])->name('order.success');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');

// Admin panel removed per user request. Orders are now handled via external OMS.
