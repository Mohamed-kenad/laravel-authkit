<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    protected $table = 'authkit_teams';

    protected $fillable = ['name', 'owner_id'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(config('authkit.user_model', \App\Models\User::class), 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(config('authkit.user_model', \App\Models\User::class), 'authkit_team_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
