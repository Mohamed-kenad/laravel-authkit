<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Kenad\AuthKit\Support\ResponseFormatter;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            ! $user instanceof MustVerifyEmail
            || $user->hasVerifiedEmail()
        ) {
            return $next($request);
        }

        return ResponseFormatter::error(
            'Your email address is not verified.',
            403
        );
    }
}
