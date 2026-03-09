<?php

declare(strict_types=1);

namespace Kenad\AuthKit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int    $id
 * @property int    $user_id
 * @property int    $token_id
 * @property string $name
 * @property string $platform
 * @property \Illuminate\Support\Carbon|null $last_active_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Device extends Model
{
    protected $table = 'authkit_devices';

    protected $fillable = [
        'user_id',
        'token_id',
        'name',
        'platform',
        'last_active_at',
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('authkit.user_model'));
    }
}
