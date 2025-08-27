<?php

namespace App\Http\Controllers;
use App\Models\Task;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        // You can pass data to the view if needed
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

            // Only send non-completed tasks to the calendar
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
                    'end' => $task->due_date,
                    'className' => $statusClass,
                    'extendedProps' => [
                        'assigned_to' => $task->assignedUser ? $task->assignedUser->name : 'Unassigned',
                        'priority' => $task->priority,
                        'status' => $task->status,
                        'description' => $task->description,
                    ]
                ];
            })->values();

            return response()->json($events);
        }
}