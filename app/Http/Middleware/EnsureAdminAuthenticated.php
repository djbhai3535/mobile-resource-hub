<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        // Must possess at least one recognized administrative role
        if (!$user->hasRole(['super-admin', 'admin', 'editor', 'author'])) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized access to administration portal.'], 403);
            }

            abort(403, 'Unauthorized. Administrative credentials required.');
        }

        return $next($request);
    }
}
