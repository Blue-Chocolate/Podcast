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
    // Use the same guard that sanctum uses
    $user = $request->user();

    if (!$user) {
        // If no user found, return 401 JSON
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    // Check the user's role
    if ($user->role !== $role) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    return $next($request);
}
}