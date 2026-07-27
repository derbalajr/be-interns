<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $tenant): Response
    {
        $user = $request->user();

        // Strictly enforce tenant isolation (403 if user tenant doesn't match the route group tenant)
        if (! $user || $user->tenant !== $tenant) {
            return response()->json([
                'message' => 'Unauthorized for this tenant module.',
            ], 403);
        }

        return $next($request);
    }
}