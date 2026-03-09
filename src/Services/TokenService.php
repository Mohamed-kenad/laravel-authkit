<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Kenad\AuthKit\Contracts\TokenServiceInterface;

use Illuminate\Support\Facades\DB;

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
        $token = $user->currentAccessToken();

        if ($token && isset($token->id)) {
            DB::table('personal_access_tokens')->where('id', $token->id)->delete();
        }
    }

    public function revokeAll(Authenticatable $user): void
    {
        DB::table('personal_access_tokens')
            ->where('tokenable_type', get_class($user))
            ->where('tokenable_id', $user->getKey())
            ->delete();
    }

    public function revokeById(Authenticatable $user, int $tokenId): bool
    {
        return (bool) DB::table('personal_access_tokens')
            ->where('tokenable_type', get_class($user))
            ->where('tokenable_id', $user->getKey())
            ->where('id', $tokenId)
            ->delete();
    }
}
