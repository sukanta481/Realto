<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCompanyAccess
{
    /**
     * Handle an incoming request.
     * Ensures the user has access to the requested company's data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'User is not associated with any company',
            ], 403);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact your administrator.',
            ], 403);
        }

        // Add company_id to request for easy access in controllers
        $request->merge(['company_id' => $user->company_id]);

        return $next($request);
    }
}
