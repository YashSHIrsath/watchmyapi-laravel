<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasEncryptedId;

class Monitor extends Model
{
    use HasFactory;
    use HasEncryptedId;

    protected $table = 'tbl_monitors';

    protected $fillable = ['project_id', 'name', 'url', 'status'];

    protected $appends = ['encrypted_id'];

    /**
     * Get the project that owns the monitor.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Get the company that owns the monitor (via project).
     */
    public function company()
    {
        return $this->project->company();
    }

    /**
     * Get the checks for the monitor.
     */
    public function checks(): HasMany
    {
        return $this->hasMany(MonitorCheck::class, 'monitor_id');
    }
}
