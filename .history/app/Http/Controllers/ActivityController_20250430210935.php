<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Project;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function getProjectActivities(Project $project)
    {
        try {
            \Log::info('Getting project activities', [
                'project_id' => $project->id,
                'user_id' => auth()->id()
            ]);
            
            $query = Activity::with(['user', 'task'])
                ->where('project_id', $project->id)
                ->orderBy('created_at', 'desc');

            // Apply type filter if provided
            if (request()->has('type')) {
                $query->where('type', request('type'));
            }
            
            $activities = $query->get();

            \Log::info('Activities retrieved', [
                'count' => $activities->count(),
                'activities' => $activities->toArray()
            ]);

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
                    'user' => [
                        'id' => $activity->user->id,
                        'name' => $activity->user->name,
                    ],
                    'getFormattedMessage' => $activity->getFormattedMessage(),
                ];
            });

            return response()->json($transformedActivities);
        } catch (\Exception $e) {
            \Log::error('Error retrieving activities', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
            'task_id' => 'nullable|exists:tasks,id',
            'changes' => 'required|array'
        ]);

        \Log::debug('Storing activity:', [
            'request_data' => $request->all(),
            'changes' => json_encode($validated['changes'])
        ]);

        $activity = Activity::create([
            'user_id' => auth()->id(),
            'project_id' => $project->id,
            'task_id' => $validated['task_id'] ?? null,
            'type' => $validated['type'],
            'description' => $validated['description'],
            'changes' => $validated['changes']
        ]);

        return response()->json($activity->load('user'));
    }
}