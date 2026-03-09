<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Token Expiration
    |--------------------------------------------------------------------------
    | Number of minutes before a Sanctum token expires. Set to null for no
    | expiration (not recommended for production APIs).
    */
    'token_expiration' => env('AUTHKIT_TOKEN_EXPIRATION', 60 * 24 * 7), // 7 days

    /*
    |--------------------------------------------------------------------------
    | Login Rate Limiting
    |--------------------------------------------------------------------------
    | Maximum number of login attempts allowed per minute per IP address
    | before the request is throttled.
    */
    'rate_limit' => [
        'max_attempts' => env('AUTHKIT_RATE_LIMIT', 5),
        'decay_minutes' => env('AUTHKIT_RATE_DECAY', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    | When enabled, users must verify their email address before they can
    | access protected routes.
    */
    'email_verification' => env('AUTHKIT_EMAIL_VERIFICATION', true),

    /*
    |--------------------------------------------------------------------------
    | Device Management
    |--------------------------------------------------------------------------
    | When enabled, the package will track the devices used to log in.
    | Users can list and revoke individual device sessions.
    */
    'device_management' => env('AUTHKIT_DEVICE_MANAGEMENT', true),

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    | All package routes will be prefixed with this value.
    | Example: /api/auth/login
    */
    'route_prefix' => env('AUTHKIT_ROUTE_PREFIX', 'auth'),

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    | Middleware applied to all package routes.
    */
    'route_middleware' => ['api'],

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    | The Eloquent model used for authentication. Change this if you use a
    | custom User model in your application.
    */
    'user_model' => env('AUTHKIT_USER_MODEL', \App\Models\User::class),

    /*
    |--------------------------------------------------------------------------
    | Token Abilities
    |--------------------------------------------------------------------------
    | Default abilities (scopes) assigned to newly created tokens.
    */
    'token_abilities' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Audit Logging
    |--------------------------------------------------------------------------
    | When enabled, auth events (login, logout, failed login) are logged
    | to laravel.log.
    */
    'audit_log' => env('AUTHKIT_AUDIT_LOG', true),

];
