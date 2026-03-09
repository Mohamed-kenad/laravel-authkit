<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Kenad\AuthKit\Models\Role;
use Kenad\AuthKit\Models\Permission;

trait HasAuthKitRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'authkit_role_user')->withTimestamps();
    }

    public function assignRole(string|Role $role): void
    {
        if (is_string($role)) {
            $role = Role::firstOrCreate(['name' => $role]);
        }

        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function removeRole(string|Role $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        if ($role) {
            $this->roles()->detach($role->id);
        }
    }

    public function hasRole(string $role): bool
    {
        return $this->roles->contains('name', $role);
    }

    public function hasPermissionTo(string $permission): bool
    {
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permission)) {
                return true;
            }
        }
        return false;
    }
}
