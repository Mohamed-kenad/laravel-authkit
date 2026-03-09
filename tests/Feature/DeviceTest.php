<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Kenad\AuthKit\DTOs\RegisterData;
use Kenad\AuthKit\Facades\AuthKit;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Register and login a user, get token
    AuthKit::register(new RegisterData('kenad', 'kenad@example.com', 'Password1!'));

    $this->token = $this->postJson('/auth/login', [
        'email'       => 'kenad@example.com',
        'password'    => 'Password1!',
        'device_name' => 'My iPhone',
        'platform'    => 'ios',
    ])->json('data.access_token');
});

it('can list active devices', function () {
    $response = $this->withToken($this->token)->getJson('/auth/devices');

    $response->assertStatus(200)
             ->assertJsonPath('success', true)
             ->assertJsonStructure([
                 'data' => [
                     '*' => ['id', 'name', 'platform', 'last_active_at'],
                 ],
             ]);
});

it('returns at least one device after login', function () {
    $devices = $this->withToken($this->token)->getJson('/auth/devices')->json('data');

    expect($devices)->toHaveCount(1)
        ->and($devices[0]['name'])->toBe('My iPhone')
        ->and($devices[0]['platform'])->toBe('ios');
});

it('can revoke a device', function () {
    $devices = $this->withToken($this->token)->getJson('/auth/devices')->json('data');
    $deviceId = $devices[0]['id'];

    $response = $this->withToken($this->token)->deleteJson("/auth/devices/{$deviceId}");

    $response->assertStatus(200)
             ->assertJsonPath('success', true);

    // Should now have 0 devices
    $remaining = $this->withToken($this->token)->getJson('/auth/devices')->json('data');
    expect($remaining)->toBeEmpty();
});

it('returns 404 when revoking non-existent device', function () {
    $response = $this->withToken($this->token)->deleteJson('/auth/devices/9999');

    $response->assertStatus(404)
             ->assertJsonPath('success', false);
});
