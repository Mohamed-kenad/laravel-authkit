<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Sanctum\PersonalAccessToken;

interface TokenServiceInterface
{
    /**
     * Create a new Sanctum token for a user with configured expiration.
     */
    public function create(Authenticatable $user, string $name, array $abilities = ['*']): string;

    /**
     * Revoke the currently authenticated token.
     */
    public function revokeCurrent(Authenticatable $user): void;

    /**
     * Revoke all tokens for a user.
     */
    public function revokeAll(Authenticatable $user): void;

    /**
     * Revoke a specific token by ID.
     */
    public function revokeById(Authenticatable $user, int $tokenId): bool;
}
