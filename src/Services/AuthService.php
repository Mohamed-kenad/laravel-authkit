<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Kenad\AuthKit\Actions\LoginUser;
use Kenad\AuthKit\Actions\LogoutUser;
use Kenad\AuthKit\Actions\RegisterUser;
use Kenad\AuthKit\Contracts\AuthServiceInterface;
use Kenad\AuthKit\DTOs\LoginData;
use Kenad\AuthKit\DTOs\RegisterData;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private readonly RegisterUser $registerUser,
        private readonly LoginUser    $loginUser,
        private readonly LogoutUser   $logoutUser,
    ) {}

    public function register(RegisterData $data): Authenticatable
    {
        return $this->registerUser->execute($data);
    }

    /**
     * @return array{user: Authenticatable, token: string}
     */
    public function login(LoginData $data, ?string $deviceName = null): array
    {
        return $this->loginUser->execute($data, $deviceName);
    }

    public function logout(Authenticatable $user): void
    {
        $this->logoutUser->execute($user, allDevices: false);
    }

    public function logoutAll(Authenticatable $user): void
    {
        $this->logoutUser->execute($user, allDevices: true);
    }
}
