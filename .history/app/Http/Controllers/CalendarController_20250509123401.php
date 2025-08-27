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
        $tasks = Task::with('assignedUser')
            ->whereNotNull('start_date')
            ->whereNotNull('due_date')
            ->get();

        $events = $tasks->map(function ($task) {
            // Map task status to appropriate CSS class
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
                    'status' => $task->status
                ]
            ];
        });

        return response()->json($events);
    }
}