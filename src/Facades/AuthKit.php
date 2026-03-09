<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Contracts\Auth\Authenticatable register(\Kenad\AuthKit\DTOs\RegisterData $data)
 * @method static array login(\Kenad\AuthKit\DTOs\LoginData $data, ?string $deviceName = null)
 * @method static void logout(\Illuminate\Contracts\Auth\Authenticatable $user)
 * @method static void logoutAll(\Illuminate\Contracts\Auth\Authenticatable $user)
 *
 * @see \Kenad\AuthKit\Services\AuthService
 */
class AuthKit extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'authkit';
    }
}
