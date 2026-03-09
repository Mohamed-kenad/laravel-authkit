<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Tests;

use Illuminate\Database\Schema\Blueprint;
use Kenad\AuthKit\AuthKitServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure users table exists for migrations that modify it
        $this->setUpDatabase();
        
        // Load package specific migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function setUpDatabase(): void
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    protected function getPackageProviders($app): array
    {
        return [
            AuthKitServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Use in-memory SQLite for fast tests
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('authkit.email_verification', false);
        $app['config']->set('authkit.device_management', true);
        $app['config']->set('authkit.rate_limit.max_attempts', 100); // avoid throttle in tests
    }
}
