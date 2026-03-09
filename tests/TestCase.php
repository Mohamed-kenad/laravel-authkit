<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kenad\AuthKit\AuthKitServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/../vendor/laravel/sanctum/database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            AuthKitServiceProvider::class,
            \Laravel\Sanctum\SanctumServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('authkit.user_model', \Kenad\AuthKit\Tests\Fixtures\User::class);
        $app['config']->set('auth.providers.users.model', \Kenad\AuthKit\Tests\Fixtures\User::class);
        
        $app['config']->set('authkit.email_verification', false);
        $app['config']->set('authkit.device_management', true);
        $app['config']->set('authkit.rate_limit.max_attempts', 100);
    }

    protected function tearDown(): void
    {
        if ($this->app) {
            $this->app['auth']->forgetGuards();
        }
        parent::tearDown();
    }
}
