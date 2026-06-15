<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'show'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    // Tela de troca de senha do primeiro acesso (liberada pelo ForcePasswordChange).
    Route::get('password/change', [PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('password/change', [PasswordChangeController::class, 'update'])->name('password.update');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    Route::resource('trips', TripController::class)->except('show');
    Route::resource('vehicles', VehicleController::class)->except('show');
    Route::resource('drivers', DriverController::class)->except('show');
    Route::resource('users', UserController::class)->except('show');

    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
});
