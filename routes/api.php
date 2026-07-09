<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PublicComplaintController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Loaded by bootstrap/app.php withRouting(api: ...) under the "api"
| middleware group with an automatic "/api" prefix (Laravel default).
| Every route below is additionally namespaced under /v1.
|
*/

Route::prefix('v1')->group(function () {
    // Public (no auth)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('/track/{ticketNumber}', [PublicComplaintController::class, 'track']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Complaints
        Route::get('/complaints', [ComplaintController::class, 'index']);
        Route::get('/complaints/{complaint}', [ComplaintController::class, 'show']);
        Route::get('/complaints/{complaint}/history', [ComplaintController::class, 'history']);
        Route::post('/complaints', [ComplaintController::class, 'store'])
            ->middleware('role:masyarakat');
        Route::post('/complaints/{complaint}/verify', [ComplaintController::class, 'verify'])
            ->middleware('role:kominfo');
        Route::post('/complaints/{complaint}/dispose', [ComplaintController::class, 'dispose'])
            ->middleware('role:kominfo');
        Route::post('/complaints/{complaint}/handle', [ComplaintController::class, 'handle'])
            ->middleware('role:opd|camat');
        Route::post('/complaints/{complaint}/respond', [ComplaintController::class, 'respond'])
            ->middleware('role:kominfo');

        // Activities
        Route::get('/activities', [ActivityController::class, 'index']);
        Route::post('/activities', [ActivityController::class, 'store'])
            ->middleware('role:opd|camat');
        Route::post('/activities/{activity}/verify', [ActivityController::class, 'verify'])
            ->middleware('role:kominfo');
        Route::post('/activities/{activity}/publish', [ActivityController::class, 'publish'])
            ->middleware('role:kominfo');

        // Dashboard
        Route::get('/dashboard/statistics', [DashboardController::class, 'statistics']);
        Route::get('/dashboard/performance', [DashboardController::class, 'performance'])
            ->middleware('role:kominfo|bupati|wakil_bupati|sekda');

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);

        // Users (Kominfo only)
        Route::middleware('role:kominfo')->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::post('/users', [UserController::class, 'store']);
            Route::put('/users/{user}', [UserController::class, 'update']);
            Route::delete('/users/{user}', [UserController::class, 'destroy']);
        });
    });
});
