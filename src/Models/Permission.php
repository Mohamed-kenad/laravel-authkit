<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $table = 'authkit_permissions';

    protected $fillable = ['name'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'authkit_permission_role');
    }
}
