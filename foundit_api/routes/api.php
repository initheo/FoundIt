<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ClaimController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth Routes (Public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

// Protected Routes (Require Token)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto']);
    Route::delete('/profile/photo', [ProfileController::class, 'deletePhoto']);

    // Leaderboard
    Route::get('/leaderboard', [ProfileController::class, 'leaderboard']);

    // Activities
    Route::get('/activities', [ActivityController::class, 'index']);

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);

    // Items
    Route::get('/items', [ItemController::class, 'index']);
    Route::get('/items/stats', [ItemController::class, 'statistics']);
    Route::get('/items/my', [ItemController::class, 'myItems']);
    Route::get('/items/{id}', [ItemController::class, 'show']);
    Route::post('/items', [ItemController::class, 'store']);
    Route::put('/items/{id}', [ItemController::class, 'update']);
    Route::put('/items/{id}/status', [ItemController::class, 'updateStatus']);
    Route::delete('/items/{id}', [ItemController::class, 'destroy']);
    Route::post('/items/{id}/photos', [ItemController::class, 'addPhoto']);
    Route::delete('/items/{id}/photos/{photoId}', [ItemController::class, 'deletePhoto']);
    Route::get('/items/{id}/claims', [ClaimController::class, 'index']);

    // Claims
    Route::post('/claims', [ClaimController::class, 'store']);
    Route::get('/claims/my', [ClaimController::class, 'myClaims']);
    Route::put('/claims/{id}/approve', [ClaimController::class, 'approve']);
    Route::put('/claims/{id}/reject', [ClaimController::class, 'reject']);
});
