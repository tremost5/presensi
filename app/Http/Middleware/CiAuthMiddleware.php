<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CiAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('isLoggedIn')) {
            return redirect('/login');
        }

        return $next($request);
    }
}
