<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use App\Models\Project;
use App\Services\NotificationService;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProjectAssignedMail;


class TaskController extends Controller
{
    private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('permission:view tasks')->only([
        'show', 'getProjectTasks'
        ]);
        $this->middleware('permission:create tasks')->only([
            'store', 'storeFromCalendar'
        ]);
        $this->middleware('permission:edit tasks')->only([
            'update'
        ]);
        $this->middleware('permission:delete tasks')->only([
            'destroy', 'deleteUserTasks'
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'project_id' => 'required|exists:projects,id',
                'task_name' => 'required|string|max:255',
                'start_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:start_date',
                'key_persons' => 'nullable|string',
                'priority' => 'required|in:High,Normal,Low',
                'status' => 'required|in:Completed,For Checking,For Revision,Deferred',
                'budget' => 'required|numeric|min:0',
                'assigned_to' => 'required|exists:users,id',
                'source_of_funding' => 'nullable|string|in:DRRM-F,LDF,NTA,For funding,Others',
                'other_funding_source' => 'nullable|string|max:255',
                'parent_task_id' => 'nullable|exists:tasks,id',
                'subtasks' => 'nullable|array',
                'subtasks.*.task_name' => 'required|string|max:255',
                'subtasks.*.start_date' => 'required|date',
                'subtasks.*.due_date' => 'required|date',
                'subtasks.*.priority' => 'required|in:High,Normal,Low',
                'subtasks.*.status' => 'required|in:Completed,For Checking,For Revision,Deferred',
                'subtasks.*.budget' => 'required|numeric|min:0',
                'subtasks.*.source_of_funding' => 'nullable|string|in:DRRM-F,LDF,NTA,For funding,Others',
                'subtasks.*.other_funding_source' => 'nullable|string|max:255'
            ]);

            $task = Task::create($validated);          
            $task->load('project', 'assignedUser');

            // Send email to the assigned user
                $user = $task->assignedUser;
                $project = $task->project;
                if ($user && $project) {
                    Mail::to($user->email)->send(new ProjectAssignedMail($user, $project));
                }

            // Create notification for the assigned user
            $this->notificationService->createForUser(
                $validated['assigned_to'],
                'task_assigned',
                'New Task Assigned',
                "You have been assigned a task: {$task->task_name} in project: {$task->project->proj_name}",
                route('projects.show', $task->project_id)
            );

            // Log main task creation
            activity()
                ->causedBy(auth()->user())
                ->performedOn($task)
                ->withProperties([
                    'project_id' => $validated['project_id'],
                    'changes' => [
                        'task_name' => ['new' => $task->task_name],
                        'status' => ['new' => $task->status],
                        'priority' => ['new' => $task->priority],
                        'assigned_to' => ['new' => $task->assigned_to],
                        'start_date' => ['new' => $task->start_date],
                        'due_date' => ['new' => $task->due_date],
                        'budget' => ['new' => $task->budget]
                    ]
                ])
                ->log("Created task: {$task->task_name}");

            // Handle subtasks
            $subtasks = [];
            if (isset($validated['subtasks']) && is_array($validated['subtasks'])) {
                foreach ($validated['subtasks'] as $subtaskData) {
                    $subtaskData['project_id'] = $validated['project_id'];
                    $subtaskData['assigned_to'] = $validated['assigned_to'];
                    $subtaskData['parent_task_id'] = $task->id;
                    
                    // Only keep funding fields for POW projects
                    $project = Project::find($validated['project_id']);
                    if ($project && $project->proj_type !== 'POW') {
                        unset($subtaskData['source_of_funding'], $subtaskData['other_funding_source']);
                    }
                    $subtask = Task::create($subtaskData);
                    $subtasks[] = $subtask;

                    // Log each subtask creation
                    activity()
                        ->causedBy(auth()->user())
                        ->performedOn($subtask)
                        ->withProperties([
                            'project_id' => $validated['project_id'],
                            'changes' => [
                                'task_name' => ['new' => $subtask->task_name],
                                'parent_task_name' => $task->task_name,
                                'status' => ['new' => $subtask->status],
                                'priority' => ['new' => $subtask->priority],
                                'start_date' => ['new' => $subtask->start_date],
                                'due_date' => ['new' => $subtask->due_date],
                                'budget' => ['new' => $subtask->budget]
                            ]
                        ])
                        ->log("Added subtask: {$subtask->task_name}");
                }
            }

            $task = $task->fresh(['project', 'assignedUser', 'subtasks.assignedUser', 'subtasks.project']);
            event(new \App\Events\TaskAssigned($task));

            return response()->json([
                'message' => 'Task created successfully',
                'task' => $task->load('subtasks'),
                'subtasks' => $subtasks
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error creating task:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getProjectTasks($projectId)
    {
        $tasks = Task::where('project_id', $projectId)
            ->whereNull('parent_task_id')
            ->with(['assignedUser', 'subtasks' => function($query) {
                $query->with('assignedUser');
            }])
            ->get()
            ->map(function($task) {
                return [
                    'id' => $task->id,
                    'task_name' => $task->task_name,
                    'start_date' => $task->start_date,
                    'due_date' => $task->due_date,
                    'priority' => $task->priority,
                    'status' => $task->status,
                    'budget' => $task->budget,
                    'assigned_to' => $task->assigned_to,
                    'assigned_user' => $task->assignedUser,
                    'completion_time' => $task->completion_time,
                    'source_of_funding' => $task->source_of_funding,
                    'other_funding_source' => $task->other_funding_source,
                    'subtasks' => $task->subtasks->map(function($subtask) use ($task) {
                        $arr = [
                            'id' => $subtask->id,
                            'task_name' => $subtask->task_name,
                            'start_date' => $subtask->start_date,
                            'due_date' => $subtask->due_date,
                            'priority' => $subtask->priority,
                            'status' => $subtask->status,
                            'budget' => $subtask->budget,
                            'assigned_to' => $subtask->assigned_to,
                            'assigned_user' => $subtask->assignedUser,
                            'completion_time' => $subtask->completion_time,
                        ];
                        if ($task->project && $task->project->proj_type === 'POW') {
                            $arr['source_of_funding'] = $subtask->source_of_funding;
                            $arr['other_funding_source'] = $subtask->other_funding_source;
                        }
                        return $arr;
                    }),
                ];
            })
            ->groupBy('assigned_to');

        return response()->json($tasks);
    }

    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
            $validated = $request->validate([
                'task_name' => 'required|string|max:255',
                'start_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:start_date',
                'priority' => 'required|in:High,Normal,Low',
                'status' => 'required|in:Completed,For Checking,For Revision,Deferred',
                'budget' => 'required|numeric|min:0',
                'assigned_to' => 'required|exists:users,id',
                'source_of_funding' => 'nullable|string|in:DRRM-F,LDF,NTA,For funding,Others',
                'other_funding_source' => 'nullable|string|max:255',
                'subtasks' => 'nullable|array',
                'subtasks.*.task_name' => 'required|string|max:255',
                'subtasks.*.start_date' => 'required|date',
                'subtasks.*.due_date' => 'required|date',
                'subtasks.*.priority' => 'required|in:High,Normal,Low',
                'subtasks.*.status' => 'required|in:Completed,For Checking,For Revision,Deferred',
                'subtasks.*.budget' => 'required|numeric|min:0',
                'subtasks.*.source_of_funding' => 'nullable|string|in:DRRM-F,LDF,NTA,For funding,Others',
                'subtasks.*.other_funding_source' => 'nullable|string|max:255'
            ]);

            // Store old values for activity log
            $oldValues = $task->toArray();
            $oldStatus = $task->status;

            // Update main task
            $task->update($validated);
            if ($validated['status'] === 'Completed' && !$task->completion_time) {
                $task->completion_time = now();
                $task->save();
            } elseif ($validated['status'] !== 'Completed') {
                $task->completion_time = null;
                $task->save();
            }
            $task->load('project', 'assignedUser');

            // Log main task changes
            $changes = [];
            foreach ($validated as $field => $newValue) {
                if ($field !== 'subtasks' && $oldValues[$field] != $newValue) {
                    $changes[$field] = [
                        'old' => $oldValues[$field],
                        'new' => $newValue
                    ];
                }
            }

            if (!empty($changes)) {
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($task)
                    ->withProperties([
                        'project_id' => $task->project_id,
                        'changes' => $changes
                    ])
                    ->log("Updated task: {$task->task_name}");
            }

            // Handle subtasks
            $subtasks = [];
            if (isset($validated['subtasks']) && is_array($validated['subtasks'])) {
                $existingSubtaskIds = $task->subtasks()->pluck('id')->toArray();
                $updatedSubtaskIds = [];
                
                foreach ($validated['subtasks'] as $subtaskData) {
                    \Log::debug('Processing subtask data', [
                    'has_id' => isset($subtaskData['id']),
                    'id_value' => $subtaskData['id'] ?? 'not set',
                    'in_existing' => isset($subtaskData['id']) ? in_array($subtaskData['id'], $existingSubtaskIds) : false,
                    'existing_ids' => $existingSubtaskIds,
                ]);

                    $project = $task->project;
                    if ($project && $project->proj_type !== 'POW') {
                        unset($subtaskData['source_of_funding'], $subtaskData['other_funding_source']);
                    }
                    if (!empty($subtaskData['id']) && in_array($subtaskData['id'], $existingSubtaskIds)) {
                        // Update existing subtask only if something changed
                        $subtask = Task::find($subtaskData['id']);
                        $oldSubtaskValues = $subtask->toArray();
                
                        $fieldsToTrack = ['task_name', 'start_date', 'due_date', 'priority', 'status', 'budget', 'source_of_funding', 'other_funding_source'];
                        $project = $task->project;
                        if ($project && $project->proj_type === 'POW') {
                            $fieldsToTrack[] = 'source_of_funding';
                            $fieldsToTrack[] = 'other_funding_source';
                        }

                        $subtaskChanges = [];
                        foreach ($fieldsToTrack as $field) {
                            if (
                                isset($subtaskData[$field]) &&
                                isset($oldSubtaskValues[$field]) &&
                                $oldSubtaskValues[$field] !== $subtaskData[$field] 
                            ) {
                                $subtaskChanges[$field] = [
                                    'old' => $oldSubtaskValues[$field],
                                    'new' => $subtaskData[$field]
                                ];
                            }
                        }
                
                        if (!empty($subtaskChanges)) {
                            $subtask->update($subtaskData);
                            if (isset($subtaskData['status'])) {
                                if ($subtaskData['status'] === 'Completed' && !$subtask->completion_time) {
                                    $subtask->completion_time = now();
                                    $subtask->save();
                                } elseif ($subtaskData['status'] !== 'Completed') {
                                    $subtask->completion_time = null;
                                    $subtask->save();
                                }
                            }
                            activity()
                            ->causedBy(auth()->user())
                            ->performedOn($subtask)
                            ->withProperties([
                                'project_id' => $task->project_id,
                                'changes' => array_merge($subtaskChanges, [
                                    'parent_task_name' => $task->task_name
                                ])
                            ])
                            ->log("Updated subtask: {$subtask->task_name} of task \"{$task->task_name}\"");
                        }
                        // Always add to updatedSubtaskIds and subtasks for response
                        $updatedSubtaskIds[] = $subtask->id;
                        $subtasks[] = $subtask;
                    } 
                    else if (!empty($subtaskData['task_name'])) {
                    // Try to find an existing subtask with this name
                    $existingSubtask = $task->subtasks()
                        ->where('task_name', '=', $subtaskData['task_name'])
                        ->first();
                        
                    if ($existingSubtask) {
                        $subtask = $existingSubtask;
                        $oldSubtaskValues = $subtask->toArray();
                        
                        $fieldsToTrack = ['task_name', 'start_date', 'due_date', 'priority', 'status', 'budget', 'source_of_funding', 'other_funding_source'];
                        $project = $task->project;
                        if ($project && $project->proj_type === 'POW') {
                            $fieldsToTrack[] = 'source_of_funding';
                            $fieldsToTrack[] = 'other_funding_source';
                        }    
                        $subtaskChanges = [];
                            foreach ($fieldsToTrack as $field) {
                                if (
                                    isset($subtaskData[$field]) &&
                                    isset($oldSubtaskValues[$field]) &&
                                    $oldSubtaskValues[$field] !== $subtaskData[$field]
                                ) {
                                    $subtaskChanges[$field] = [
                                        'old' => $oldSubtaskValues[$field],
                                        'new' => $subtaskData[$field]
                                    ];
                                }
                            }
                        
                        if (!empty($subtaskChanges)) {
                            $subtask->update($subtaskData);
                            if (isset($subtaskData['status'])) {
                                if ($subtaskData['status'] === 'Completed' && !$subtask->completion_time) {
                                    $subtask->completion_time = now();
                                    $subtask->save();
                                } elseif ($subtaskData['status'] !== 'Completed') {
                                    $subtask->completion_time = null;
                                    $subtask->save();
                                }
                            }
                            activity()
                                ->causedBy(auth()->user())
                                ->performedOn($subtask)
                                ->withProperties([
                                    'project_id' => $task->project_id,
                                    'changes' => array_merge($subtaskChanges, [
                                        'parent_task_name' => $task->task_name
                                    ])
                                ])
                                ->log("Updated subtask: {$subtask->task_name} of task \"{$task->task_name}\"");
                        }
                        
                        $updatedSubtaskIds[] = $subtask->id;
                        $subtasks[] = $subtask;
                        continue;
                    }else {
                        // Create new subtask
                        $subtaskData['project_id'] = $task->project_id;
                        $subtaskData['assigned_to'] = $validated['assigned_to'];
                        $subtaskData['parent_task_id'] = $task->id;
                
                        $subtask = Task::create($subtaskData);
                        $subtasks[] = $subtask;
                
                        $project = $task->project;
                        if ($project && $project->proj_type !== 'POW') {
                            unset($subtaskData['source_of_funding'], $subtaskData['other_funding_source']);
                        }
                
                        \Log::info('Processing subtask', [
                            'subtask_id' => $subtaskData['id'] ?? null,
                            'existing_ids' => $existingSubtaskIds,
                            'data' => $subtaskData
                        ]);
                
                        // Log new subtask
                        Activity::create([
                            'causer_id' => auth()->id(),
                            'subject_id' => $subtask->id,
                            'subject_type' => Task::class,
                            'description' => "Added subtask: {$subtask->task_name}",
                            'properties' => [
                                'project_id' => $task->project_id,
                                'changes' => [
                                    'task_name' => ['new' => $subtask->task_name],
                                    'parent_task_name' => $task->task_name
                                ]
                            ]
                        ]);

                        $updatedSubtaskIds[] = $subtask->id;
                    }
                }
                // Handle deleted subtasks
                $subtasksToDelete = array_diff($existingSubtaskIds, $updatedSubtaskIds);
                foreach ($subtasksToDelete as $subtaskId) {
                    $subtask = Task::find($subtaskId);
                    if ($subtask) {
                        activity()
                            ->causedBy(auth()->user())
                            ->performedOn($subtask)
                            ->withProperties([
                                'project_id' => $task->project_id,
                                'changes' => [
                                    'task_name' => ['old' => $subtask->task_name],
                                    'parent_task_name' => $task->task_name
                                ]
                            ])
                            ->log("Deleted subtask: {$subtask->task_name}");
                        $subtask->delete();
                    }
                }
            }
        }

            // After all subtask logic:
            $task = $task->fresh(['project', 'assignedUser', 'subtasks.assignedUser', 'subtasks.project']);
            event(new \App\Events\TaskAssigned($task));
            
            return response()->json([
                'message' => 'Task updated successfully',
                'task' => $task->load('subtasks'),
                'subtasks' => $subtasks
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating task:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'task_id' => $id,
                'user_id' => auth()->id()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
            
            // Determine if this is a subtask by checking for parent_task_id
            $isSubtask = !is_null($task->parent_task_id);
            
            if ($isSubtask) {
                // Get parent task for the activity log
                $parentTask = Task::find($task->parent_task_id);
                $parentTaskName = $parentTask ? $parentTask->task_name : 'unknown task';
                
                // Log subtask deletion
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($subtask)
                    ->withProperties([
                        'project_id' => $task->project_id,
                        'changes' => [
                            'task_name' => ['old' => $subtask->task_name],
                            'parent_task_name' => $task->task_name
                        ]
                    ])
                    ->log("Deleted subtask: {$subtask->task_name}");
            } else {
                // Log main task deletion
                activity()
                ->causedBy(auth()->user())
                ->performedOn($task)
                ->withProperties([
                    'project_id' => $task->project_id,
                    'changes' => [
                        'task_name' => ['old' => $task->task_name],
                        'status' => ['old' => $task->status],
                        'priority' => ['old' => $task->priority],
                        'assigned_to' => ['old' => $task->assigned_to],
                        'start_date' => ['old' => $task->start_date],
                        'due_date' => ['old' => $task->due_date],
                        'budget' => ['old' => $task->budget],
                        'source_of_funding' => ['old' => $task->source_of_funding ?? ''],
                        'other_funding_source' => ['old' => $task->other_funding_source ?? '']
                    ]
                ])
                ->log("Deleted task: {$task->task_name}");
            }
            
            // Include additional data in response if it's a subtask
            $response = [
                'message' => 'Task deleted successfully',
                'parent_task_id' => $isSubtask ? $task->parent_task_id : null
            ];
            
            $task->delete();

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteUserTasks($projectId, $userId)
    {
        try {
            // Find the project
            $project = Project::findOrFail($projectId);
            
            // Delete all tasks for this user in this project
            $tasksDeleted = $project->tasks()->where('assigned_to', $userId)->delete();
            
            // Return JSON response
            return response()->json([
                'success' => true,
                'message' => 'Tasks deleted successfully',
                'data' => [
                    'project_id' => $projectId,
                    'user_id' => $userId,
                    'tasks_deleted' => $tasksDeleted
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting tasks:', [
                'project_id' => $projectId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tasks: ' . $e->getMessage()
            ], 500);
        }
    }
    public function show(Task $task)
    {
        // Load the task's assigned user
        $task->load(['assignedUser']);
        
        // Get the activity log entry for task creation to determine who created it
        $taskCreator = Activity::where('subject_id', $task->id)
            ->where('subject_type', Task::class)
            ->where('description', 'Created task: ' . $task->task_name)
            ->with('causer:id,name,email')
            ->first();
        
        // Add creator information to the response
        $responseData = $task->toArray();
        $responseData['creator'] = null;
        
        if ($taskCreator && $taskCreator->causer) {
            $responseData['creator'] = [
                'id' => $taskCreator->causer->id,
                'name' => $taskCreator->causer->name
            ];
        }
        
        return response()->json($responseData);
    }
    public function saveSortingView(Request $request)
    {
        $validated = $request->validate([
            'column' => 'required|string',
            'order' => 'required|in:asc,desc',
            'project_id' => 'required|integer',
        ]);
        $projectId = $validated['project_id'];

        session([
            "sorting_view_{$projectId}" => [
                'column' => $validated['column'],
                'order' => $validated['order'],
            ],
        ]);

        return response()->json(['message' => 'Sorting view saved successfully']);
    }

    public function getSortingView(Request $request)
    {
        $projectId = $request->query('project_id');
        $sortingView = session("sorting_view_{$projectId}", [
            'column' => null,
            'order' => null
        ]);
        return response()->json($sortingView + ['project_id' => $projectId]);
    }
    public function resetSortingView(Request $request)
    {
        $projectId = $request->input('project_id');
        session()->forget("sorting_view_{$projectId}");
        return response()->json([
            'message' => 'Sorting preferences reset successfully',
            'data' => [
                'column' => null,
                'order' => null
            ]
        ]);
    }
    public function getCompletionTime(Task $task)
    {
        return response()->json([
            'completion_time' => $task->completion_time
        ]);
    }

    public function markCompleted(Task $task)
    {
        $task->update([
            'completion_time' => now(),
            'status' => 'Completed'
        ]);

        // Send notifications (reusing your existing notification logic)
        if ($task->status !== 'Completed') {
            // Notify the assigned user
            $this->notificationService->createForUser(
                $task->assigned_to,
                'task_completed',
                'Task Completed',
                'Task "' . $task->task_name . '" has been marked as completed',
                route('projects.show', $task->project_id)
            );

            // Notify the project owner
            if ($task->project && $task->project->user) {
                $this->notificationService->createForUser(
                    $task->project->user->id,
                    'task_completed',
                    'Task Completed',
                    'Task "' . $task->task_name . '" has been marked as completed by ' . $task->assignedUser->name,
                    route('projects.show', $task->project_id)
                );
            }
        }

        return response()->json([
            'message' => 'Task marked as completed',
            'completion_time' => $task->completion_time
        ]);
    }

    public function storeFromCalendar(Request $request)
    {
        $validated = $request->validate([
            'task_name' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'description' => 'nullable|string',
        ]);

        // Combine date and time for start and end
        $startDateTime = $validated['date'] . ' ' . $validated['start_time'];
        $endDateTime = $validated['date'] . ' ' . $validated['end_time'];

        Task::create([
            'task_name' => $validated['task_name'],
            'start_date' => $startDateTime,
            'due_date' => $endDateTime,
            'description' => $validated['description'] ?? null,
            'assigned_to' => auth()->id(),
            'project_id' => null,
            'priority' => 'Normal',
            'status' => 'For Checking',
            'budget' => 0,
        ]);

        return response()->json(['success' => true]);
    }
}
