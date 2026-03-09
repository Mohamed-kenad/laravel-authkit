<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Support;

use Illuminate\Support\Str;

final class TokenGenerator
{
    /**
     * Generate a cryptographically secure random token string.
     */
    public static function generate(int $length = 64): string
    {
        return Str::random($length);
    }

    /**
     * Generate a URL-safe base64 token (useful for email verification links).
     */
    public static function generateUrlSafe(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}
