<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Actions;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Auth\Authenticatable;
use Kenad\AuthKit\Services\TokenService;

final class LogoutUser
{
    public function __construct(
        private readonly TokenService $tokenService,
    ) {}

    public function execute(Authenticatable $user, bool $allDevices = false): void
    {
        if ($allDevices) {
            $this->tokenService->revokeAll($user);
        } else {
            $this->tokenService->revokeCurrent($user);
        }

        if (config('authkit.audit_log')) {
            logger()->info('[AuthKit] User logged out', [
                'user_id'     => $user->getKey(),
                'all_devices' => $allDevices,
            ]);
        }

        event(new Logout('sanctum', $user));

        // Clear resolved instances to avoid caching issues in tests/long-running processes
        if (app()->bound('auth')) {
            auth()->forgetGuards();
        }
    }
}
