<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Actions;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\Authenticatable;
use Kenad\AuthKit\Exceptions\AuthKitException;

final class VerifyEmail
{
    public function execute(Authenticatable $user): void
    {
        if (! $user instanceof MustVerifyEmail) {
            throw new AuthKitException('User model does not implement MustVerifyEmail.');
        }

        if ($user->hasVerifiedEmail()) {
            throw new AuthKitException('Email is already verified.');
        }

        $user->markEmailAsVerified();

        event(new Verified($user));
    }
}
