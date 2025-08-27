<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function getProjectActivities(Project $project)
    {
        try {
            $query = Activity::with(['user', 'task'])
                ->where('project_id', $project->id)
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

        // For subtask_updated, create an activity per changed field
        if ($validated['type'] === 'subtask_updated') {
            $createdActivities = [];
            foreach ($validated['changes'] as $field => $change) {
                // Only log if the field actually changed and is not just empty/null
                if (
                    isset($change['old'], $change['new']) &&
                    (
                        ($change['old'] !== $change['new']) &&
                        !(is_null($change['old']) && is_null($change['new'])) &&
                        !(trim((string)$change['old']) === '' && trim((string)$change['new']) === '')
                    )
                ) {
                    $activity = Activity::create([
                        'user_id' => auth()->id(),
                        'project_id' => $project->id,
                        'task_id' => $validated['task_id'] ?? null,
                        'type' => $validated['type'],
                        'description' => $validated['description'],
                        'changes' => array_merge(
                            [$field => $change],
                            isset($validated['changes']['parent_task_name']) ? ['parent_task_name' => $validated['changes']['parent_task_name']] : []
                        ),
                    ]);
                    $activity->load('user');
                    $responseData = $activity->toArray();
                    $responseData['formatted_message'] = $activity->getFormattedMessage();
                    $createdActivities[] = $responseData;
                }
            }
            return response()->json($createdActivities);
        }

        // Default: single activity
        $activity = Activity::create([
            'user_id' => auth()->id(),
            'project_id' => $project->id,
            'task_id' => $validated['task_id'] ?? null,
            'type' => $validated['type'],
            'description' => $validated['description'],
            'changes' => $validated['changes']
        ]);

        $activity->load('user');
        $responseData = $activity->toArray();
        $responseData['formatted_message'] = $activity->getFormattedMessage();
        
        return response()->json($responseData);
    }
}