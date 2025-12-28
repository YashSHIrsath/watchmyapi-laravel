<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorCheck extends Model
{
    protected $table = 'tbl_monitor_checks';

    protected $fillable = [
        'monitor_id',
        'checked_at',
        'http_status_code',
        'response_time_ms',
        'is_success',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
        'is_success' => 'boolean',
    ];

    /**
     * Get the monitor that owns the check.
     */
    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class, 'monitor_id');
    }
}
