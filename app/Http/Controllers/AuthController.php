<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
   public function login(LoginRequest $request)
{
    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json([
            'message' => 'Invalid credentials.',
        ], 401);
    

    }
    if (! $user->active) {
    return response()->json([
        'message' => 'Your account has been deactivated. Please contact a manager.',
    ], 403);
}

    $token = $user->createToken('api')->plainTextToken;

    return response()->json([
        'user' => new UserResource($user),
        'token' => $token,
    ]);
}

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request)
    {
        return new UserResource($request->user());
    }
}
