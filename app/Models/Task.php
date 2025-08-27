<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Task extends Model
{
    use HasFactory, LogsActivity; 

    protected $fillable = [
        'project_id',
        'task_name',
        'start_date',
        'due_date',
        'key_persons',
        'priority',
        'status',
        'budget',
        'source_of_funding',
        'other_funding_source',
        'assigned_to',
        'parent_task_id',
        'completion_time',
        'task_description',
    ];


    public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logOnly([
            'project_id',
            'task_name',
            'start_date',
            'due_date',
            'key_persons',
            'priority',
            'status',
            'budget',
            'source_of_funding',
            'other_funding_source',
            'assigned_to',
            'parent_task_id',
            'completion_time',
            'task_description',
        ])
        ->logOnlyDirty()
        ->useLogName('task');
}

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function files()
    {
        return $this->hasMany(TaskFile::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function($task) {
            $task->subtasks()->delete();
        });
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function assignee()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }
}