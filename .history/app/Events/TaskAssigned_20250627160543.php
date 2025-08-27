<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskAssigned implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function broadcastOn()
    {
        return new Channel('tasks');
    }

    public function broadcastWith()
    {
        $task = $this->task->fresh(['assignedUser', 'project', 'subtasks.assignedUser', 'subtasks.project']);
        $isOwnTask = $task->assigned_to === auth()->id();
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
            'end' => \Illuminate\Support\Carbon::parse($task->due_date)->addDay()->toDateString(),
            'className' => $statusClass . ($isOwnTask ? '' : ' other-user-task'),
            'assigned_to' => $task->assignedUser ? $task->assignedUser->name : 'Unassigned',
            'status' => $task->status,
            'priority' => $task->priority,
            'project' => $task->project ? $task->project->proj_name : 'No project',
            // Subtasks array
            'subtasks' => $task->subtasks->map(function($sub) {
                $subStatusClass = match($sub->status) {
                    'Completed' => 'event-completed',
                    'For Checking' => 'event-checking',
                    'For Revision' => 'event-revision',
                    'Deferred' => 'event-pending',
                    default => 'event-pending'
                };
                return [
                    'id' => $sub->id,
                    'title' => $sub->task_name,
                    'start' => $sub->start_date,
                    'end' => \Illuminate\Support\Carbon::parse($sub->due_date)->addDay()->toDateString(),
                    'className' => $subStatusClass,
                    'assigned_to' => $sub->assignedUser ? $sub->assignedUser->name : 'Unassigned',
                    'status' => $sub->status,
                    'priority' => $sub->priority,
                    'project' => $sub->project ? $sub->project->proj_name : 'No project',
                ];
            })->values(),
        ];
    }
}