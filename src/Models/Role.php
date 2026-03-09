<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $table = 'authkit_roles';

    protected $fillable = ['name'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'authkit_permission_role');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(config('authkit.user_model', \App\Models\User::class), 'authkit_role_user');
    }

    public function givePermissionTo(string|Permission $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::firstOrCreate(['name' => $permission]);
        }
        
        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }
}
