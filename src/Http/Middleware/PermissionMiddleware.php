<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kenad\AuthKit\Support\ResponseFormatter;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (! $request->user() || ! method_exists($request->user(), 'hasPermissionTo') || ! $request->user()->hasPermissionTo($permission)) {
            return ResponseFormatter::forbidden('You do not have the required permission.');
        }

        return $next($request);
    }
}
