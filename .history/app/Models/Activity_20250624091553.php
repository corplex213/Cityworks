<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'task_id',
        'type',
        'description',
        'changes'
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function getProjectActivities(Project $project)
{
    try {

        $query = Activity::with(['user', 'task'])
            ->where('project_id', $project->id)
            // Exclude subtask activities
            ->whereNotIn('type', ['subtask_added', 'subtask_updated', 'subtask_deleted'])
            ->orderBy('created_at', 'desc');

        // Apply type filter if provided
        if (request()->has('type')) {
            $query->where('type', request('type'));
        }
        
        $activities = $query->get();

        // Transform activities to include formatted message
        $transformedActivities = $activities->map(function($activity) {
            return [
                'id' => $activity->id,
                'user_id' => $activity->user_id,
                'project_id' => $activity->project_id,
                'task_id' => $activity->task_id,
                'type' => $activity->type,
                'description' => $activity->description,
                'changes' => $activity->changes,
                'created_at' => $activity->created_at,
                'user' => $activity->user,
                'formatted_message' => $activity->getFormattedMessage()
            ];
        });

        return response()->json($transformedActivities);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}
