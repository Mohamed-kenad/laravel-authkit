<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kenad\AuthKit\Models\Team;

trait HasAuthKitTeams
{
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'authkit_team_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function currentTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    public function belongsToTeam(Team $team): bool
    {
        return $this->teams->contains('id', $team->id) || $this->ownsTeam($team);
    }

    public function ownsTeam(Team $team): bool
    {
        return $this->id == $team->owner_id;
    }

    public function switchTeam(Team $team): bool
    {
        if (! $this->belongsToTeam($team)) {
            return false;
        }

        $this->forceFill([
            'current_team_id' => $team->id,
        ])->save();

        // If using sessions, might want to refresh session data here, 
        // but since this is an API package, we just update DB.

        return true;
    }
}
