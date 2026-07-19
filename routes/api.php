<?php

use App\Http\Controllers\AuthController;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

Route::get('/health', function () {
    return response()->json([
        'data' => [
            'status' => 'ok',
        ],
    ]);
});

Route::get('/test-user', function () {
    return new UserResource(User::findOrFail(999));
});

Route::post('/test-validation', function (Request $request) {

    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email',
    ]);

    if ($validator->fails()) {
        throw new ValidationException($validator);
    }

    return response()->json([
        'data' => [
            'message' => 'Validation passed.',
        ],
    ]);
});
