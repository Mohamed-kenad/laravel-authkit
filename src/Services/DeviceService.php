<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Kenad\AuthKit\Contracts\DeviceServiceInterface;
use Kenad\AuthKit\Models\Device;
use Kenad\AuthKit\Services\TokenService;

class DeviceService implements DeviceServiceInterface
{
    public function __construct(
        private readonly TokenService $tokenService,
    ) {}

    public function register(Authenticatable $user, string $deviceName, string $platform, int $tokenId): void
    {
        Device::updateOrCreate(
            [
                'user_id'  => $user->getKey(),
                'token_id' => $tokenId,
            ],
            [
                'name'           => $deviceName,
                'platform'       => $platform,
                'last_active_at' => now(),
            ]
        );
    }

    public function list(Authenticatable $user): Collection
    {
        return Device::where('user_id', $user->getKey())
            ->orderByDesc('last_active_at')
            ->get();
    }

    public function revoke(Authenticatable $user, int $deviceId): bool
    {
        $device = Device::where('id', $deviceId)
            ->where('user_id', $user->getKey())
            ->first();

        if (! $device) {
            return false;
        }

        // Also revoke the underlying Sanctum token
        $this->tokenService->revokeById($user, $device->token_id);

        return (bool) $device->delete();
    }
}
