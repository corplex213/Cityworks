<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    //
    protected $fillable = [
        'proj_name',
        'proj_type',
        'status',
        'archived'
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($project) {
            // Delete all tasks related to this project
            $project->tasks()->delete();
        });
    }
    
    public function details()
    {
        return $this->hasOne(Detail::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }
}
