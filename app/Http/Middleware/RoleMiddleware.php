<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            \Log::info('User not authenticated.');
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        \Log::info('User role: ' . Auth::user()->role);
        if (!in_array(Auth::user()->role, $roles)) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }
        return $next($request);
    }
}
