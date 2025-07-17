<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (!$request->user()) {

            abort(403, 'Unauthorized.');
        }

        $allowedRoles = explode('|', $roles); // Mendukung banyak role
        if (!in_array($request->user()->role, $allowedRoles)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
