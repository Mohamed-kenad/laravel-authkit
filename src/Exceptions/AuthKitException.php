<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Exceptions;

use RuntimeException;

class AuthKitException extends RuntimeException
{
    public function __construct(string $message = 'Authentication error.', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
