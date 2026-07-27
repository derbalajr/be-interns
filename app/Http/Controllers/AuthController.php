<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login
     *
     * Authenticate a user using their email and password.
     *
     * After a successful login, a Sanctum API token is returned.
     *
     * @group Authentication
     *
     * @response 200 {
     *   "user": {
     *     "id": 1,
     *     "name": "Salma Ibrahim",
     *     "email": "salma@example.com"
     *   },
     *   "token": "1|abcdefghijklmnopqrstuvwxyz"
     * }
     */
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

    /**
     * Logout
     *
     * Logs out the currently authenticated user by revoking their current API token.
     *
     * @group Authentication
     *
     * @authenticated
     *
     * @response 200 {
     *   "message": "Logged out successfully."
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Current User
     *
     * Returns the authenticated user's profile.
     *
     * @group Authentication
     *
     * @authenticated
     */
    public function me(Request $request)
    {
        return new UserResource($request->user());
    }
}
