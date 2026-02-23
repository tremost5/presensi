<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CiRoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $role = (string) (session('role_id') ?? '');

        if ($role === '') {
            return redirect('/login');
        }

        if (!in_array($role, $roles, true)) {
            return redirect('/login');
        }

        return $next($request);
    }
}
