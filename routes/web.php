<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\User\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

// ── Guest Routes ──────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// ── Logout ────────────────────────────────────────────
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Authenticated Routes ──────────────────────────────
Route::middleware('auth')->group(function () {

    // User routes
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Admin routes
    Route::prefix('admin')
        ->middleware('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

            // Phase 2: Exam CRUD routes will be added here
            // Phase 3: Question management routes will be added here
        });
});
