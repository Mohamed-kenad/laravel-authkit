<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kenad\AuthKit\Support\ResponseFormatter;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (! $request->user() || ! method_exists($request->user(), 'hasRole') || ! $request->user()->hasRole($role)) {
            return ResponseFormatter::forbidden('You do not have the required role.');
        }

        return $next($request);
    }
}
