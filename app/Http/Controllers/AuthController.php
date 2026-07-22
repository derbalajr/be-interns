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

        // Validate workspace matches user's tenant
        $workspace = $request->workspace;
        if ($workspace) {
            $expectedTenant = $workspace === 'the-address' ? 'tai' : 'marq';
            if ($user->tenant !== $expectedTenant) {
                return response()->json([
                    'message' => 'This user does not belong to the selected workspace.',
                ], 403);
            }
        }

        /** @var User $user */

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
