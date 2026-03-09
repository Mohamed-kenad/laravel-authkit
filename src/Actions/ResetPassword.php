<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Actions;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Kenad\AuthKit\Exceptions\AuthKitException;
use Kenad\AuthKit\Services\TokenService;

final class ResetPassword
{
    public function __construct(
        private readonly TokenService $tokenService,
    ) {}

    public function execute(string $token, string $email, string $password): void
    {
        $status = Password::reset(
            ['email' => $email, 'password' => $password, 'token' => $token],
            function (Authenticatable $user) use ($password) {
                $user->forceFill([
                    'password'        => Hash::make($password),
                    'remember_token'  => Str::random(60),
                ])->save();

                // Revoke all existing tokens after password reset for security
                $this->tokenService->revokeAll($user);

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new AuthKitException(__($status));
        }
    }
}
