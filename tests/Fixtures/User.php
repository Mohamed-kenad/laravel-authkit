<?php

namespace Kenad\AuthKit\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Kenad\AuthKit\Traits\HasAuthKitRoles;
use Kenad\AuthKit\Traits\HasAuthKitTeams;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasAuthKitRoles, HasAuthKitTeams;

    protected $guarded = [];

    protected $hidden = ['password', 'remember_token'];
}
