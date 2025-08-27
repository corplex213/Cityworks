<?php

namespace App\Http\Controllers;
use App\Models\Task;
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

        $tasks = Task::with('assignedUser')
            ->where('assigned_to', $user->id)
            ->whereNotNull('start_date')
            ->whereNotNull('due_date')
            ->get();

        if ($request->query('all') == 1) {
            $events = $tasks->map(function ($task) {
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
                    'end' => Carbon::parse($task->due_date)->addDay()->toDateString(), // <-- Add a day here
                    'className' => $statusClass,
                    'extendedProps' => [
                        'assigned_to' => $task->assignedUser ? $task->assignedUser->name : 'Unassigned',
                        'priority' => $task->priority,
                        'status' => $task->status,
                        'description' => $task->description,
                    ]
                ];
            })->values();
        } else {
            $events = $tasks->filter(function ($task) {
                return $task->status !== 'Completed';
            })->map(function ($task) {
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
                    'end' => Carbon::parse($task->due_date)->addDay()->toDateString(), // <-- Add a day here
                    'className' => $statusClass,
                    'extendedProps' => [
                        'assigned_to' => $task->assignedUser ? $task->assignedUser->name : 'Unassigned',
                        'priority' => $task->priority,
                        'status' => $task->status,
                        'description' => $task->description,
                    ]
                ];
            })->values();
        }

        return response()->json($events);
    }
}