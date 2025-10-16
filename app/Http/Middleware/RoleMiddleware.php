<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // If user is not authenticated, let Authenticate middleware handle it
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Check if user has the required role
        if ($user->role !== $role) {
            // For web routes, redirect or abort
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            
            // Log out and redirect to login
            Auth::logout();
            return redirect()->route('filament.admin.auth.login')
                ->with('error', 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}