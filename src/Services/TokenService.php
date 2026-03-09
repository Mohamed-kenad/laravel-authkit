<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Kenad\AuthKit\Contracts\TokenServiceInterface;

class TokenService implements TokenServiceInterface
{
    public function create(Authenticatable $user, string $name, array $abilities = ['*']): string
    {
        $expiresAt = config('authkit.token_expiration')
            ? now()->addMinutes((int) config('authkit.token_expiration'))
            : null;

        $token = $user->createToken($name, $abilities, $expiresAt);

        return $token->plainTextToken;
    }

    public function revokeCurrent(Authenticatable $user): void
    {
        // currentAccessToken() is provided by HasApiTokens
        $user->currentAccessToken()?->delete();
    }

    public function revokeAll(Authenticatable $user): void
    {
        $user->tokens()->delete();
    }

    public function revokeById(Authenticatable $user, int $tokenId): bool
    {
        return (bool) $user->tokens()->where('id', $tokenId)->delete();
    }
}
