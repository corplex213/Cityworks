<?php

namespace App\Http\Controllers;
use App\Models\Task;
use App\Models\CalendarEvent; 
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar');
    }

    public function events(Request $request)
    {
        $user = $request->user();
        $taskFilter = $request->input('task_filter', 'all');
        $userFilter = $request->input('userFilter', 'mine');
        
        // Add logging
        \Log::info('Calendar events request', [
        'user_id' => $user->id,
        'task_filter' => $taskFilter,
        'user_filter' => $userFilter,
        'request_path' => $request->path(),
        'request_method' => $request->method(),
        'user_agent' => $request->header('User-Agent')
        ]);

        // Base task query - start with current user's tasks
        $taskQuery = Task::with(['assignedUser', 'project'])->whereNotNull('start_date')
            ->whereNotNull('due_date');
        
        // Apply user filter
        if ($userFilter === 'mine') {
            $taskQuery->where('assigned_to', $user->id);
        } elseif ($userFilter === 'all') {
            // No filter, show all users' tasks
        } elseif (is_numeric($userFilter)) {
            $taskQuery->where('assigned_to', $userFilter);
        }
        
        // Get tasks
        $tasks = $taskQuery->get()
            ->filter(function ($task) {
                return $task->project !== null || $task->project_id === null;
            });
        
        // Apply task status filter in PHP for flexibility
        if ($taskFilter !== 'all') {
            $tasks = $tasks->filter(function($task) use ($taskFilter) {
                switch($taskFilter) {
                    case 'ongoing':
                        return $task->status !== 'Completed' && $task->status !== 'Deferred';
                    case 'completed':
                        return $task->status === 'Completed';
                    case 'deferred':
                        return $task->status === 'Deferred';
                }
                return true;
            });
        }
        
        $taskEvents = $tasks->map(function ($task) {
            $statusClass = match($task->status) {
                'Completed' => 'event-completed',
                'For Checking' => 'event-checking',
                'For Revision' => 'event-revision',
                'Deferred' => 'event-pending',
                default => 'event-pending'
            };
            
            // Add owner indicator to distinguish between own & others' tasks
            $isOwnTask = $task->assigned_to === request()->user()->id;
            
            return [
                'id' => $task->id,
                'title' => $task->task_name,
                'start' => $task->start_date,
                'end' => $task->due_date,
                'className' => $statusClass . ($isOwnTask ? '' : ' other-user-task'),
                'extendedProps' => [
                    'project' => $task->project ? $task->project->proj_name : 'No project',
                    'priority' => $task->priority,
                    'assigned_to' => $task->assignedUser ? $task->assignedUser->name : 'Unassigned',
                    'status' => $task->status,
                    'isOwnTask' => $isOwnTask
                ]
            ];
        })->values();
        
        // Always include calendar events for the current user
        $calendarEvents = CalendarEvent::where('user_id', $user->id)
            ->get()
            ->map(function ($event) use ($user) {
                return [
                    'id' => 'event-' . $event->id,
                    'title' => $event->title,
                    'start' => $event->start,
                    'end' => $event->end,
                    'className' => 'calendar-event',
                    'backgroundColor' => '#ffffff',
                    'textColor' => '#333333',
                    'extendedProps' => [
                        'description' => $event->description,
                        'assigned_to' => $user->name,
                        'status' => null,
                        'project' => null,
                        'priority' => null,
                        'isOwnEvent' => true
                    ]
                ];
            });

        // Merge and return
        $allEvents = $taskEvents->merge($calendarEvents)->values();
        return response()->json($allEvents);
    }

    public function getUsers()
    {
        // Get all users except the current user
        $users = \App\Models\User::where('id', '!=', auth()->id())
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        return response()->json($users);
    }
}