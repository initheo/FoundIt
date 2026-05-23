<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\DashboardWebController;
use App\Http\Controllers\Admin\CategoryWebController;
use App\Http\Controllers\Admin\UserWebController;
use App\Http\Controllers\Admin\ItemWebController;
use App\Http\Controllers\Admin\ClaimWebController;

// Redirect root to admin login
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Admin Auth Routes
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Protected Admin Web Routes
Route::middleware(['auth', 'check.role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardWebController::class, 'index'])->name('dashboard');

    // Categories CRUD
    Route::resource('categories', CategoryWebController::class);

    // Users View & Manage
    Route::resource('users', UserWebController::class)->except(['create', 'store']);

    // Items CRUD (with specific photo endpoints if needed)
    Route::resource('items', ItemWebController::class);
    Route::post('/items/{id}/photos', [ItemWebController::class, 'addPhoto'])->name('items.photos.add');
    Route::delete('/items/{id}/photos/{photoId}', [ItemWebController::class, 'deletePhoto'])->name('items.photos.delete');

    // Claims Management
    Route::resource('claims', ClaimWebController::class)->only(['index', 'show']);
    Route::post('/claims/{id}/approve', [ClaimWebController::class, 'approve'])->name('claims.approve');
    Route::post('/claims/{id}/reject', [ClaimWebController::class, 'reject'])->name('claims.reject');
});
