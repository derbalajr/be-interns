<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\HandoverController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReservationController;
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
    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    /*
    |--------------------------------------------------------------------------
    | User management
    |--------------------------------------------------------------------------
    */

    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Roles and permissions
    |--------------------------------------------------------------------------
    */

    Route::apiResource('roles', RoleController::class);
    Route::get('/permissions', [PermissionController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | Shared inventory routes
    |--------------------------------------------------------------------------
    |
    | These routes must be accessible to authenticated users who have the
    | view-units permission. TAI users need them to browse inventory before
    | adding a unit to a lead shortlist.
    |
    */

    Route::get('/units', [UnitController::class, 'index']);
    Route::get('/units/{unit}', [UnitController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | Tenant: TAI
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Tenant: MARQ
    |--------------------------------------------------------------------------
    */

    Route::middleware('tenant:marq')->group(function () {
        Route::apiResource('projects', ProjectController::class);

        Route::post('/units', [UnitController::class, 'store']);
        Route::match(['put', 'patch'], '/units/{unit}', [UnitController::class, 'update']);
        Route::delete('/units/{unit}', [UnitController::class, 'destroy']);

        Route::patch('/units/{unit}/reserve', [
            UnitController::class,
            'markReserved',
        ]);

        Route::patch('/units/{unit}/sell', [
            UnitController::class,
            'markSold',
        ]);

        // Reservations
        Route::get('/reservations', [ReservationController::class, 'index']);
        Route::post('/reservations', [ReservationController::class, 'store']);
        Route::get('/reservations/{reservation}', [ReservationController::class, 'show']);
        Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel']);

        // Clients
        Route::apiResource('clients', ClientController::class);
        Route::post('/clients/process-national-id', [ClientController::class, 'processNationalId']);

        // Handovers
        Route::get('/handovers', [HandoverController::class, 'index']);
        Route::post('/handovers', [HandoverController::class, 'store']);
        Route::get('/handovers/{handover}', [HandoverController::class, 'show']);
        Route::post('/handovers/{handover}/complete', [HandoverController::class, 'complete']);
    });
});
