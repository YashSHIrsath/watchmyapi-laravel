<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    // Public routes
    Route::post('login', [AuthController::class, 'login'])->middleware(['web', 'throttle:5,1']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->middleware('web');
        Route::get('me', [AuthController::class, 'me']);
        
        // Session Awareness
        Route::get('sessions', [AuthController::class, 'sessions']);
        Route::delete('sessions/others', [AuthController::class, 'revokeOthers']);
        Route::delete('sessions/{id}', [AuthController::class, 'revokeSpecific']);
    });
});

// Admin Management
Route::prefix('admin')->middleware(['auth:api', 'admin'])->group(function () {
    Route::get('/companies', [\App\Http\Controllers\Api\AdminController::class, 'index']);
});

// Project Management
Route::prefix('projects')->middleware(['auth:api', 'company'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\ProjectController::class, 'index']);
    Route::post('/', [\App\Http\Controllers\Api\ProjectController::class, 'store']);
    Route::put('/{id}', [\App\Http\Controllers\Api\ProjectController::class, 'update']);
    Route::delete('/{id}', [\App\Http\Controllers\Api\ProjectController::class, 'destroy']);
});

// Monitor Management (Company-Scoped)
Route::prefix('monitors')->middleware(['auth:api', 'company'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\MonitorController::class, 'index']);
    Route::post('/', [\App\Http\Controllers\Api\MonitorController::class, 'store']);
    Route::get('/{id}', [\App\Http\Controllers\Api\MonitorController::class, 'show']);
    Route::put('/{id}', [\App\Http\Controllers\Api\MonitorController::class, 'update']);
    Route::delete('/{id}', [\App\Http\Controllers\Api\MonitorController::class, 'destroy']);
    Route::get('/{id}/checks', [\App\Http\Controllers\Api\MonitorCheckController::class, 'index']);
});

// Statistics & Metrics
Route::middleware(['auth:api', 'company'])->group(function () {
    Route::get('/stats/dashboard', [\App\Http\Controllers\Api\StatsController::class, 'dashboard']);
    Route::get('/stats/monitor/{id}', [\App\Http\Controllers\Api\StatsController::class, 'monitor']);
});
