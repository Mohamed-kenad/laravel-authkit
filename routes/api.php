<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Kenad\AuthKit\Http\Controllers\AuthController;
use Kenad\AuthKit\Http\Controllers\DeviceController;

$prefix     = config('authkit.route_prefix', 'auth');
$middleware = config('authkit.route_middleware', ['api']);

// ─── Public Routes ───────────────────────────────────────────────────────────
Route::prefix($prefix)
    ->middleware($middleware)
    ->group(function () {

        Route::post('register',         [AuthController::class, 'register']);
        Route::post('login',            [AuthController::class, 'login']);
        Route::post('forgot-password',  [AuthController::class, 'forgotPassword']);
        Route::post('reset-password',   [AuthController::class, 'resetPassword']);

    });

// ─── Protected Routes (Sanctum) ──────────────────────────────────────────────
Route::prefix($prefix)
    ->middleware([...$middleware, 'auth:sanctum'])
    ->group(function () {

        Route::post('logout',           [AuthController::class, 'logout']);
        Route::post('logout-all',       [AuthController::class, 'logoutAll']);
        Route::get('me',                [AuthController::class, 'me']);
        Route::get('email/verify',      [AuthController::class, 'verifyEmail']);

        // Device management
        if (config('authkit.device_management')) {
            Route::get('devices',           [DeviceController::class, 'index']);
            Route::delete('devices/{id}',   [DeviceController::class, 'revoke']);
        }

    });
