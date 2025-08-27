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
        $task = $this->task->fresh(['assignedUser', 'project']);
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
            // Add any other fields you use in extendedProps
        ];
    }
}