<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Password;
use Kenad\AuthKit\Exceptions\AuthKitException;

final class SendPasswordResetLink
{
    public function execute(string $email): void
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new AuthKitException(__($status));
        }
    }
}
