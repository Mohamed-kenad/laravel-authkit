<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

interface DeviceServiceInterface
{
    /**
     * Register (or update) a device entry for this login session.
     */
    public function register(Authenticatable $user, string $deviceName, string $platform, int $tokenId): void;

    /**
     * Return all active devices for a user.
     */
    public function list(Authenticatable $user): Collection;

    /**
     * Revoke access from a specific device (deletes the device + its token).
     */
    public function revoke(Authenticatable $user, int $deviceId): bool;
}
