<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BoothController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\EventController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1'); // Rate limit: 5 attempts per minute
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Public Routes
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
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
    Route::post('/booths/{id}/remove', [BoothController::class, 'removeBooth'])->name('booths.remove');
    Route::post('/booths/update-external-view', [BoothController::class, 'updateExternalView'])->name('booths.update-external-view');
    Route::post('/booths/{id}/save-position', [BoothController::class, 'savePosition'])->name('booths.save-position');
    Route::post('/booths/save-all-positions', [BoothController::class, 'saveAllPositions'])->name('booths.save-all-positions');
    Route::post('/booths/upload-floorplan', [BoothController::class, 'uploadFloorplan'])->name('booths.upload-floorplan');
    Route::post('/booths/remove-floorplan', [BoothController::class, 'removeFloorplan'])->name('booths.remove-floorplan');
    Route::get('/booths/check-duplicate/{boothNumber}', [BoothController::class, 'checkDuplicate'])->name('booths.check-duplicate');
    Route::get('/booths/zone-settings/{zoneName}', [BoothController::class, 'getZoneSettings'])->name('booths.get-zone-settings');
    Route::post('/booths/zone-settings/{zoneName}', [BoothController::class, 'saveZoneSettings'])->name('booths.save-zone-settings');
    Route::post('/booths/create-in-zone/{zoneName}', [BoothController::class, 'createBoothInZone'])->name('booths.create-in-zone');
    Route::post('/booths/delete-in-zone/{zoneName}', [BoothController::class, 'deleteBoothsInZone'])->name('booths.delete-in-zone');
    Route::post('/booths/book-booth', [BoothController::class, 'bookBooth'])->name('booths.book-booth');

    // Clients
    Route::resource('clients', ClientController::class);
    
    // Export Routes
    Route::get('/export/booths', [ExportController::class, 'exportBooths'])->name('export.booths');
    Route::get('/export/clients', [ExportController::class, 'exportClients'])->name('export.clients');
    Route::get('/export/bookings', [ExportController::class, 'exportBookings'])->name('export.bookings');

    // Books
    Route::resource('books', BookController::class);
    Route::post('/books/booking', [BookController::class, 'booking'])->name('books.booking');
    Route::post('/books/upbooking', [BookController::class, 'upbooking'])->name('books.upbooking');
    Route::get('/books/info/{id}', [BookController::class, 'info'])->name('books.info');

    // Categories
    Route::resource('categories', CategoryController::class);
    Route::post('/categories/create-category', [CategoryController::class, 'createCategory'])->name('categories.create-category');
    Route::post('/categories/update-category', [CategoryController::class, 'updateCategory'])->name('categories.update-category');
    Route::delete('/categories/delete-category/{id}', [CategoryController::class, 'deleteCategory'])->name('categories.delete-category');
    Route::post('/categories/create-sub-category', [CategoryController::class, 'createSubCategory'])->name('categories.create-sub-category');
    Route::post('/categories/update-sub-category', [CategoryController::class, 'updateSubCategory'])->name('categories.update-sub-category');

    // Admin Routes
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/users/{id}/status', [UserController::class, 'status'])->name('users.status');
        Route::get('/users/{id}/password', [UserController::class, 'updatePassword'])->name('users.password');
        Route::post('/users/{id}/password', [UserController::class, 'updatePassword'])->name('users.password.update');
        
        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/cache/clear', [SettingsController::class, 'clearCache'])->name('settings.cache.clear');
        Route::post('/settings/config/clear', [SettingsController::class, 'clearConfig'])->name('settings.config.clear');
        Route::post('/settings/route/clear', [SettingsController::class, 'clearRoute'])->name('settings.route.clear');
        Route::post('/settings/view/clear', [SettingsController::class, 'clearView'])->name('settings.view.clear');
        Route::post('/settings/clear-all', [SettingsController::class, 'clearAll'])->name('settings.clear-all');
        Route::post('/settings/optimize', [SettingsController::class, 'optimize'])->name('settings.optimize');
        
        // Booth Default Settings API
        Route::get('/settings/booth-defaults', [SettingsController::class, 'getBoothDefaults'])->name('settings.booth-defaults');
        Route::post('/settings/booth-defaults', [SettingsController::class, 'saveBoothDefaults'])->name('settings.booth-defaults.save');
        
        // Canvas Settings API
        Route::get('/settings/canvas', [SettingsController::class, 'getCanvasSettings'])->name('settings.canvas');
        Route::post('/settings/canvas', [SettingsController::class, 'saveCanvasSettings'])->name('settings.canvas.save');
    });
});

// ========================================
// Admin Event Management System Routes
// ========================================
Route::prefix('admin')->group(function () {
    // Admin Authentication Routes
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login'])->middleware('throttle:5,1'); // Rate limit: 5 attempts per minute
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

    // Protected Admin Routes
    Route::middleware(['admin.auth'])->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        
        // Events Management
        Route::resource('events', EventController::class)->names([
            'index' => 'admin.events.index',
            'create' => 'admin.events.create',
            'store' => 'admin.events.store',
            'show' => 'admin.events.show',
            'edit' => 'admin.events.edit',
            'update' => 'admin.events.update',
            'destroy' => 'admin.events.destroy',
        ]);
    });
});
