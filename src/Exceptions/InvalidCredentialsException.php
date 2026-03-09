<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Exceptions;

class InvalidCredentialsException extends AuthKitException
{
    public function __construct()
    {
        parent::__construct('The provided credentials are incorrect.', 401);
    }
}
