<?php

declare(strict_types=1);

namespace Kenad\AuthKit;

use Illuminate\Support\ServiceProvider;
use Kenad\AuthKit\Contracts\AuthServiceInterface;
use Kenad\AuthKit\Contracts\DeviceServiceInterface;
use Kenad\AuthKit\Contracts\TokenServiceInterface;
use Kenad\AuthKit\Services\AuthService;
use Kenad\AuthKit\Services\DeviceService;
use Kenad\AuthKit\Services\TokenService;

class AuthKitServiceProvider extends ServiceProvider
{
    /**
     * Register package services into the container.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/authkit.php',
            'authkit'
        );

        // Bind contracts to implementations — easily swappable
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(TokenServiceInterface::class, TokenService::class);
        $this->app->bind(DeviceServiceInterface::class, DeviceService::class);

        // Named binding for the Facade
        $this->app->bind('authkit', function ($app) {
            return $app->make(AuthServiceInterface::class);
        });
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Register middlewares for roles/permissions
        app('router')->aliasMiddleware('authkit.role', \Kenad\AuthKit\Http\Middleware\RoleMiddleware::class);
        app('router')->aliasMiddleware('authkit.permission', \Kenad\AuthKit\Http\Middleware\PermissionMiddleware::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/authkit.php' => config_path('authkit.php'),
            ], 'authkit-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'authkit-migrations');
        }
    }
}
