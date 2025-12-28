<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (Company $company) {
            $company->projects()->create([
                'name' => 'Default Project',
                'is_default' => true,
            ]);
        });
    }

    protected $table = 'tbl_companies';

    protected $fillable = [
        'name',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    /**
     * Get the projects for the company.
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'company_id');
    }
}
