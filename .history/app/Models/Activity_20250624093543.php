<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'task_id',
        'type',
        'description',
        'changes'
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

public function getFormattedMessage()
{
    \Log::debug('Activity getFormattedMessage', [
        'activity_id' => $this->id,
        'type' => $this->type,
        'task_id' => $this->task_id,
        'changes' => $this->getRawOriginal('changes')
    ]);

    // Remove subtask activity log messages
    if (in_array($this->type, ['subtask_added', 'subtask_updated', 'subtask_deleted'])) {
        return null;
    }

    // Only log for main tasks (not subtasks)
    // Assumes Task model has a parent_id field: parent_id == null means main task
    if ($this->task && !empty($this->task->parent_id)) {
        // This is a subtask, skip logging
        return null;
    }

    $userName = $this->user->name;
    $decodedChanges = json_decode($this->getRawOriginal('changes'), true);

    $taskName = $this->task ? $this->task->task_name : 'unknown task';

    switch ($this->type) {
        case 'added_engineer':
            $decodedChanges = json_decode($this->getRawOriginal('changes'), true);
            $targetUserName = $decodedChanges['target_user_name'] ?? 'unknown user';
            return "<strong>{$userName}</strong> created task table for <strong>{$targetUserName}</strong>";
        
        case 'removed_engineer':
            $decodedChanges = json_decode($this->getRawOriginal('changes'), true);
            $targetUserName = $decodedChanges['target_user_name'] ?? 'unknown user';
            return "<strong>{$userName}</strong> removed task table for <strong>{$targetUserName}</strong>";

        case 'created':
            $decodedChanges = json_decode($this->getRawOriginal('changes'), true);
            $assignedUserId = $decodedChanges['assigned_to']['new'] ?? null;
            $targetUserName = User::find($assignedUserId)->name ?? 'unknown user';
            return "<strong>{$userName}</strong> created task \"{$taskName}\" assigned to <strong>{$targetUserName}</strong>";
    
        case 'updated':
            $rawChanges = json_decode($this->getRawOriginal('changes'), true);
            if (empty($rawChanges)) {
                return "<strong>{$userName}</strong> updated task \"{$taskName}\"";
            }
                
            $messages = [];
            foreach ($rawChanges as $field => $change) {
                if (!isset($change['old']) || !isset($change['new']) || $change['old'] === $change['new']) {
                    continue;
                }
                    
                $oldValue = $change['old'];
                $newValue = $change['new'];
                    
                // Format the field name for display
                $fieldDisplay = ucfirst(str_replace('_', ' ', $field));
                    
                // Special formatting for specific fields
                switch ($field) {
                    case 'start_date':
                    case 'due_date':
                        $oldValue = date('M d, Y', strtotime($oldValue));
                        $newValue = date('M d, Y', strtotime($newValue));
                        break;
                    case 'budget':
                        $oldValue = number_format($oldValue, 2);
                        $newValue = number_format($newValue, 2);
                        break;
                    case 'source_of_funding':
                        $fieldDisplay = 'Source of funding';
                        if (empty($oldValue)) $oldValue = 'None';
                        if (empty($newValue)) $newValue = 'None';
                        break;
                    case 'other_funding_source':
                        $fieldDisplay = 'Other funding source';
                        if (empty($oldValue)) $oldValue = 'None';
                        if (empty($newValue)) $newValue = 'None';
                        break;
                }
                $messages[] = "<strong>{$userName}</strong> changed {$fieldDisplay} from \"{$oldValue}\" to \"{$newValue}\" in task \"{$taskName}\"";
            }
            return !empty($messages) ? implode("<br>", $messages) : "<strong>{$userName}</strong> updated task \"{$taskName}\"";
    
        case 'deleted':
            return "<strong>{$userName}</strong> deleted task \"{$taskName}\"";
            
        default:
            return "<strong>{$userName}</strong> performed an action on task \"{$taskName}\"";
    }
}
}
