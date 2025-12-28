<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasEncryptedId;

class Project extends Model
{
    use HasFactory;
    use HasEncryptedId;

    protected $table = 'tbl_projects';

    protected $fillable = [
        'company_id',
        'name',
        'is_default',
    ];

    protected $appends = ['encrypted_id'];

    /**
     * Get the company that owns the project.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Get the monitors for the project.
     */
    public function monitors(): HasMany
    {
        return $this->hasMany(Monitor::class, 'project_id');
    }
}
