<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Actions;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Kenad\AuthKit\DTOs\RegisterData;

final class RegisterUser
{
    public function execute(RegisterData $data): Authenticatable
    {
        /** @var class-string<Authenticatable&\Illuminate\Database\Eloquent\Model> $userModel */
        $userModel = config('authkit.user_model');

        /** @var Authenticatable $user */
        $user = $userModel::create([
            'name'     => $data->name,
            'email'    => $data->email,
            'password' => Hash::make($data->password),
        ]);

        // Fire the registered event so Laravel can send email verification
        event(new Registered($user));

        return $user;
    }
}
