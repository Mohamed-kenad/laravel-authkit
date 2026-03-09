<p align="center">
    <img src="https://raw.githubusercontent.com/kenad/laravel-authkit/main/art/logo.svg" alt="Laravel AuthKit Logo" width="400">
</p>

<p align="center">
    <a href="https://packagist.org/packages/kenad/laravel-authkit"><img src="https://img.shields.io/packagist/v/kenad/laravel-authkit.svg?style=flat-square" alt="Latest Version on Packagist"></a>
    <a href="https://github.com/kenad/laravel-authkit/actions"><img src="https://img.shields.io/github/actions/workflow/status/kenad/laravel-authkit/run-tests.yml?branch=main&label=tests&style=flat-square" alt="GitHub Tests Action Status"></a>
    <a href="https://packagist.org/packages/kenad/laravel-authkit"><img src="https://img.shields.io/packagist/dt/kenad/laravel-authkit.svg?style=flat-square" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/kenad/laravel-authkit"><img src="https://img.shields.io/packagist/php-v/kenad/laravel-authkit?style=flat-square" alt="PHP from Packagist"></a>
    <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/License-MIT-success.svg?style=flat-square" alt="License"></a>
</p>

# The Ultimate Laravel API Authentication Starter Kit 🚀

Laravel AuthKit is a powerful, zero-configuration API authentication package built on top of Laravel Sanctum. 
It instantly supercharges your Laravel application with enterprise-grade features while remaining incredibly easy to use.

Stop writing boilerplate auth code for every new project. Just install AuthKit, and within 2 minutes, your API is fully equipped.

## ✨ Features

- 🔐 **Complete Auth API**: Register, Login, Logout, Forgot Password, Reset Password.
- 📱 **Device Management**: Track logins by device, let users view active sessions and revoke them remotely.
- 👮‍♂️ **Roles & Permissions**: Built-in, lightweight module to manage user authorization.
- 🏢 **Teams (Multi-tenancy)**: Native support for users belonging to multiple teams.
- 🔑 **Token Abilities**: Granular control over Sanctum token scopes right from the login request.
- 🛡️ **Rate Limiting**: Intelligent throttling to prevent brute-force attacks.
- ✉️ **Email Verification**: Seamless flow mapped to Laravel's native events.
- 📝 **Audit Logging**: Traceable logins and logouts for security compliance.
- 🎨 **Consistent API Responses**: Standardized JSON format across all auth endpoints.

---

## 📦 Installation

REQUIREMENT: PHP 8.2+ and Laravel 10/11/12+.

You can install the package via composer:

```bash
composer require kenad/laravel-authkit
```

Publish the package configuration and migrations:

```bash
php artisan vendor:publish --provider="Kenad\AuthKit\AuthKitServiceProvider"
```

Run the migrations:

```bash
php artisan migrate
```

---

## ⚙️ Configuration

AuthKit is deeply customizable. In `config/authkit.php`, you can adjust token expiration, rate limiting rules, enable/disable modules (like Device Management and Audit Logging), and set your custom User model.

```php
return [
    'token_expiration' => 60 * 24 * 7, // 7 days
    
    'rate_limit' => [
        'max_attempts' => 5,
        'decay_minutes' => 1,
    ],
    
    'device_management' => true,
    'email_verification' => true,
    'audit_log' => true,
];
```

---

## 👩‍💻 Usage

AuthKit handles the API routing automatically (prefixed with `/api/auth/` by default).

### 1️⃣ Authentication API

Simply hit these plug-and-play endpoints:

- `POST /api/auth/register`: `{ name, email, password, password_confirmation }`
- `POST /api/auth/login`: `{ email, password, device_name, platform, abilities }`
- `POST /api/auth/logout`: (Requires Bearer Token)
- `POST /api/auth/logout-all`: Logout from all devices.
- `GET /api/auth/me`: Get current user info.

**Standardized Response Format:**
```json
{
  "success": true,
  "message": "Login successful.",
  "data": {
    "user": { "id": 1, "name": "kenad", "email": "kenad@example.com" },
    "access_token": "1|abcdef123456",
    "token_type": "Bearer",
    "expires_in": 10080
  }
}
```

### 2️⃣ Roles and Permissions

Add the `HasAuthKitRoles` trait to your User model:

```php
use Kenad\AuthKit\Traits\HasAuthKitRoles;

class User extends Authenticatable
{
    use HasAuthKitRoles;
}
```

Now you can intuitively assign roles and check permissions:

```php
$user->assignRole('admin');
$user->assignRole('editor');

$role = Role::create(['name' => 'writer']);
$role->givePermissionTo('publish articles');

$user->hasRole('admin'); // true
$user->hasPermissionTo('publish articles'); // true
```

*Middleware included!* Protect your routes easily:
```php
Route::get('/admin', [AdminController::class, 'index'])->middleware('authkit.role:admin');
```

### 3️⃣ Teams (Multi-Tenancy)

Building a SaaS? Use the `HasAuthKitTeams` trait on your User model:

```php
use Kenad\AuthKit\Traits\HasAuthKitTeams;

class User extends Authenticatable
{
    use HasAuthKitTeams;
}
```

Manage teams fluently:
```php
$team = Team::create(['name' => 'Acme Corp', 'owner_id' => $user->id]);

$user->belongsToTeam($team); // true
$user->ownsTeam($team); // true
$user->switchTeam($team); // Set active context
```

### 4️⃣ Device Management API

If enabled in the config, users can manage their active login sessions:

- `GET /api/auth/devices`: List all active devices.
- `DELETE /api/auth/devices/{id}`: Revoke access for a specific device.

### 5️⃣ Facade Magic

Prefer writing custom controllers? AuthKit exposes a beautiful Facade that handles the complex business logic for you:

```php
use Kenad\AuthKit\Facades\AuthKit;
use Kenad\AuthKit\DTOs\LoginData;

// Register explicitly
$user = AuthKit::register(new RegisterData('John', 'john@example.com', 'secret'));

// Login cleanly
$result = AuthKit::login(new LoginData('john@example.com', 'secret'), 'iPhone 15');
return response()->json(['token' => $result['token']]);
```

---

## 🧪 Testing

AuthKit is built with **Pest PHP** and is highly decoupled into Actions and Contracts for supreme testability.

```bash
composer test
```

## 🏗️ Architecture

Under the hood, AuthKit uses a highly scalable modular architecture inspired by Spatie:
- **Actions** pattern for atomic, single-responsibility business logic (`LoginUser`, `ResetPassword`).
- **DTOs** (Data Transfer Objects) for strong typing between the HTTP layer and Application layer.
- **Contracts/Services** for swappable implementations. Let's say you want to change how tokens are generated? Just implement `TokenServiceInterface` and re-bind it in your AppServiceProvider!

---

## 🤝 Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 🔐 Security Vulnerabilities
If you discover any security-related issues, please email kenad@example.com instead of using the issue tracker.

## 📄 License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
