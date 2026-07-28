<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
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

    // User management
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    // Roles and permissions
    Route::apiResource('roles', RoleController::class);
    Route::get('/permissions', [PermissionController::class, 'index']);

    // Tenant: TAI
    Route::middleware('tenant:tai')->group(function () {
        Route::patch('/leads/{lead}/assign', [
            LeadController::class,
            'assign',
        ]);

        Route::patch('/leads/{lead}/stage', [
            LeadController::class,
            'changeStage',
        ]);
        Route::get('/leads/{lead}/shortlist', [
            LeadController::class,
            'shortlist',
        ]);

        Route::post('/leads/{lead}/shortlist/{unit}', [
            LeadController::class,
            'addToShortlist',
        ]);

        Route::delete('/leads/{lead}/shortlist/{unit}', [
            LeadController::class,
            'removeFromShortlist',
        ]);

        Route::apiResource('leads', LeadController::class);
        Route::apiResource('deals', DealController::class);
    });

    // Tenant: MARQ
    Route::middleware('tenant:marq')->group(function () {
        Route::apiResource('projects', ProjectController::class);

        Route::patch('/units/{unit}/reserve', [
            UnitController::class,
            'markReserved',
        ]);

        Route::patch('/units/{unit}/sell', [
            UnitController::class,
            'markSold',
        ]);

        Route::apiResource('units', UnitController::class);
    });
});
