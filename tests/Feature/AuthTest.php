<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Kenad\AuthKit\DTOs\LoginData;
use Kenad\AuthKit\DTOs\RegisterData;
use Kenad\AuthKit\Facades\AuthKit;

uses(RefreshDatabase::class);

// ─── Register ────────────────────────────────────────────────────────────────

it('can register a new user via facade', function () {
    $user = AuthKit::register(new RegisterData(
        name: 'kenad Dev',
        email: 'kenad@example.com',
        password: 'Password1!',
    ));

    expect($user)
        ->not->toBeNull()
        ->and($user->email)->toBe('kenad@example.com');

    $this->assertDatabaseHas('users', ['email' => 'kenad@example.com']);
});

it('can register via API endpoint', function () {
    $response = $this->postJson('/auth/register', [
        'name'                  => 'kenad Dev',
        'email'                 => 'kenad@example.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response->assertStatus(201)
             ->assertJsonPath('success', true)
             ->assertJsonPath('data.email', 'kenad@example.com');
});

it('returns validation error for duplicate email on register', function () {
    $this->postJson('/auth/register', [
        'name'                  => 'kenad Dev',
        'email'                 => 'kenad@example.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response = $this->postJson('/auth/register', [
        'name'                  => 'Another User',
        'email'                 => 'kenad@example.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response->assertStatus(422);
});

// ─── Login ───────────────────────────────────────────────────────────────────

it('can login a user and receive a token', function () {
    AuthKit::register(new RegisterData('kenad', 'kenad@example.com', 'Password1!'));

    $response = $this->postJson('/auth/login', [
        'email'    => 'kenad@example.com',
        'password' => 'Password1!',
    ]);

    $response->assertStatus(200)
             ->assertJsonPath('success', true)
             ->assertJsonStructure([
                 'data' => ['user', 'access_token', 'token_type'],
             ]);
});

it('returns 401 for invalid credentials', function () {
    $response = $this->postJson('/auth/login', [
        'email'    => 'nobody@example.com',
        'password' => 'WrongPassword1!',
    ]);

    $response->assertStatus(401)
             ->assertJsonPath('success', false);
});

// ─── Me ──────────────────────────────────────────────────────────────────────

it('can fetch authenticated user via /me endpoint', function () {
    AuthKit::register(new RegisterData('kenad', 'kenad@example.com', 'Password1!'));

    $loginResult = $this->postJson('/auth/login', [
        'email'    => 'kenad@example.com',
        'password' => 'Password1!',
    ])->json('data.access_token');

    $response = $this->withToken($loginResult)->getJson('/auth/me');

    $response->assertStatus(200)
             ->assertJsonPath('success', true)
             ->assertJsonPath('data.email', 'kenad@example.com');
});

// ─── Logout ──────────────────────────────────────────────────────────────────

it('can logout a user', function () {
    AuthKit::register(new RegisterData('kenad', 'kenad@example.com', 'Password1!'));

    $token = $this->postJson('/auth/login', [
        'email'    => 'kenad@example.com',
        'password' => 'Password1!',
    ])->json('data.access_token');

    $response = $this->withToken($token)->postJson('/auth/logout');

    $response->assertStatus(200)
             ->assertJsonPath('success', true);

    // Token should now be invalid
    $this->withToken($token)->getJson('/auth/me')->assertStatus(401);
});

it('can logout from all devices', function () {
    AuthKit::register(new RegisterData('kenad', 'kenad@example.com', 'Password1!'));

    $token1 = $this->postJson('/auth/login', ['email' => 'kenad@example.com', 'password' => 'Password1!'])->json('data.access_token');
    $token2 = $this->postJson('/auth/login', ['email' => 'kenad@example.com', 'password' => 'Password1!'])->json('data.access_token');

    $this->withToken($token1)->postJson('/auth/logout-all')->assertStatus(200);

    // Both tokens should now be revoked
    $this->withToken($token2)->getJson('/auth/me')->assertStatus(401);
});
