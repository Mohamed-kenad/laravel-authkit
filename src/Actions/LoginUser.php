<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Actions;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Kenad\AuthKit\DTOs\LoginData;
use Kenad\AuthKit\Exceptions\InvalidCredentialsException;
use Kenad\AuthKit\Exceptions\TooManyAttemptsException;
use Kenad\AuthKit\Services\DeviceService;
use Kenad\AuthKit\Services\TokenService;

final class LoginUser
{
    public function __construct(
        private readonly TokenService $tokenService,
        private readonly DeviceService $deviceService,
    ) {}

    /**
     * @return array{user: Authenticatable, token: string}
     */
    public function execute(LoginData $data, ?string $deviceName = null, ?string $platform = null): array
    {
        $throttleKey = 'authkit:login:' . $data->email;
        $maxAttempts = config('authkit.rate_limit.max_attempts', 5);

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw TooManyAttemptsException::withRetryAfter($seconds);
        }

        /** @var class-string<Authenticatable&\Illuminate\Database\Eloquent\Model> $userModel */
        $userModel = config('authkit.user_model');

        $user = $userModel::where('email', $data->email)->first();

        if (! $user || ! Hash::check($data->password, $user->password)) {
            RateLimiter::hit($throttleKey, config('authkit.rate_limit.decay_minutes', 1) * 60);
            throw new InvalidCredentialsException();
        }

        RateLimiter::clear($throttleKey);

        $resolvedDeviceName = $deviceName ?? 'web';
        $abilities = $data->abilities ?? config('authkit.token_abilities', ['*']);
        
        $plainToken = $this->tokenService->create($user, $resolvedDeviceName, $abilities);

        // Track the device if device management is enabled
        if (config('authkit.device_management')) {
            $token = $user->tokens()->latest()->first();
            $this->deviceService->register($user, $resolvedDeviceName, $platform ?? 'unknown', $token->id);
        }

        if (config('authkit.audit_log')) {
            logger()->info('[AuthKit] User logged in', ['user_id' => $user->getKey(), 'device' => $resolvedDeviceName]);
        }

        event(new Login('sanctum', $user, false));

        return ['user' => $user, 'token' => $plainToken];
    }
}
