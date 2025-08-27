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
        'archived',
        'source_of_funding',
        'other_funding_source'
    ];

    public function details()
    {
        return $this->hasOne(Detail::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }
}
