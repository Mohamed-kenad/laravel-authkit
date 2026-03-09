<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Exceptions;

class TooManyAttemptsException extends AuthKitException
{
    public function __construct(int $retryAfterSeconds = 60)
    {
        parent::__construct(
            "Too many login attempts. Please try again in {$retryAfterSeconds} seconds.",
            429
        );
    }

    public static function withRetryAfter(int $seconds): self
    {
        return new self($seconds);
    }
}
