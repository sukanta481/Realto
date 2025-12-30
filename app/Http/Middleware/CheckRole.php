<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Check if the user has one of the required roles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user || !$user->role) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Admin has access to everything
        if ($user->role->name === 'admin') {
            return $next($request);
        }

        // Check if user's role is in the allowed roles
        if (!in_array($user->role->name, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to perform this action',
            ], 403);
        }

        return $next($request);
    }
}
