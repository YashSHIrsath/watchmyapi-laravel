<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRefreshToken extends Model
{
    protected $table = 'tbl_user_refresh_tokens';

    protected $fillable = [
        'user_id',
        'token_hash',
        'is_revoked',
        'expires_at',
        'last_used_at',
        'user_agent',
        'ip_address',
    ];

    protected $casts = [
        'is_revoked' => 'boolean',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_revoked', false)
                     ->where('expires_at', '>', now());
    }
}
