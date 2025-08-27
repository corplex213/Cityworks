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

            // Fetch project tasks (existing code)
            $tasks = Task::with(['assignedUser', 'project'])
                ->where('assigned_to', $user->id)
                ->whereNotNull('start_date')
                ->whereNotNull('due_date')
                ->get()
                ->filter(function ($task) {
                    return $task->project !== null || $task->project_id === null;
                });

            $taskEvents = $tasks->map(function ($task) {
                $statusClass = match($task->status) {
                    'Completed' => 'event-completed',
                    'For Checking' => 'event-checking',
                    'For Revision' => 'event-revision',
                    'Deferred' => 'event-pending',
                    default => 'event-pending'
                };

                return [
                    'id' => $task->id,
                    'title' => $task->task_name,
                    'start' => $task->start_date,
                    'end' => $task->due_date,
                    'className' => $statusClass,
                    'extendedProps' => [
                        'project' => $task->project ? $task->project->proj_name : 'No project',
                        'priority' => $task->priority,
                        'assigned_to' => $task->assignedUser ? $task->assignedUser->name : 'Unassigned',
                        'status' => $task->status,
                    ]
                ];
            })->values();

            // Fetch calendar-only events
            $calendarEvents = CalendarEvent::where('user_id', $user->id)->get()->map(function ($event) use ($user) {
                return [
                    'id' => 'event-' . $event->id, // prefix to distinguish from tasks
                    'title' => $event->title,
                    'start' => $event->start,
                    'end' => $event->end,
                    'className' => 'calendar-event',
                    'extendedProps' => [
                        'description' => $event->description,
                        'assigned_to' => $user->name,
                        'status' => null,
                        'project' => null,
                        'priority' => null,
                    ]
                ];
            });

            // Merge and return
            $allEvents = $taskEvents->merge($calendarEvents)->values();
            return response()->json($allEvents);
        }
}