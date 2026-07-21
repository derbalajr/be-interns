<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

// Health check
Route::get('/health', function () {
    return response()->json([
        'data' => [
            'status' => 'ok',
        ],
    ]);
});

// Public auth
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:6,1');

// Protected API routes
Route::middleware('auth:api')->group(function () {
    
    // Auth management
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Roles & Permissions CRUD
    Route::apiResource('roles', RoleController::class);
    Route::get('/permissions', [PermissionController::class, 'index']);

    // Tenant: TAI
    Route::middleware('tenant:tai')->group(function () {
        Route::apiResource('leads', LeadController::class);
        Route::patch('/leads/{lead}/assign', [LeadController::class, 'assign']);    });

    // Tenant: MARQ
    Route::middleware('tenant:marq')->group(function () {
        Route::apiResource('projects', ProjectController::class);
    });

});