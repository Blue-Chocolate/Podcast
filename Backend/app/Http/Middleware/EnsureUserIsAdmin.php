<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'admin') {
            abort(403, 'You are not authorized to access this area.');
        }

        return $next($request);
    }
}
