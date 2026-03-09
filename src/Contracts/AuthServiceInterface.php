<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Kenad\AuthKit\DTOs\LoginData;
use Kenad\AuthKit\DTOs\RegisterData;

interface AuthServiceInterface
{
    /**
     * Register a new user and optionally send email verification.
     *
     * @return Authenticatable The newly created user model instance
     */
    public function register(RegisterData $data): Authenticatable;

    /**
     * Authenticate a user and return a Sanctum token string.
     *
     * @return array{user: Authenticatable, token: string}
     */
    public function login(LoginData $data, ?string $deviceName = null): array;

    /**
     * Revoke the current token (logout from current device).
     */
    public function logout(Authenticatable $user): void;

    /**
     * Revoke all tokens for the user (logout from all devices).
     */
    public function logoutAll(Authenticatable $user): void;
}
