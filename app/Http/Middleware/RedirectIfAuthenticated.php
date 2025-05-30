<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Peran yang akan diarahkan ke dashboard
            $roles = [ 'Admin', 'Bar', 'Kitchen'];

            if ($user->hasAnyRole($roles)) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
