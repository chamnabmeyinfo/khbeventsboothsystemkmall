<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BoothController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Public Routes
Route::get('/', function () {
    return redirect()->route('booths.index');
});

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Booths
    Route::resource('booths', BoothController::class);
    Route::get('/my-booths', [BoothController::class, 'myBooths'])->name('booths.my-booths');
    Route::post('/booths/{id}/confirm', [BoothController::class, 'confirmReservation'])->name('booths.confirm');
    Route::post('/booths/{id}/clear', [BoothController::class, 'clearReservation'])->name('booths.clear');
    Route::post('/booths/{id}/paid', [BoothController::class, 'markPaid'])->name('booths.paid');

    // Clients
    Route::resource('clients', ClientController::class);

    // Books
    Route::resource('books', BookController::class);

    // Categories
    Route::resource('categories', CategoryController::class);

    // Admin Routes
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class);
    });
});
