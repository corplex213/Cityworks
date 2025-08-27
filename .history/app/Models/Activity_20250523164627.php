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
        $userName = $this->user->name;
        $taskName = $this->task ? $this->task->task_name : '';

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
                        }
                        $messages[] = "<strong>{$userName}</strong> changed {$fieldDisplay} from \"{$oldValue}\" to \"{$newValue}\" in task \"{$taskName}\"";
                    }
                    return !empty($messages) ? implode("<br>", $messages) : "<strong>{$userName}</strong> updated task \"{$taskName}\"";
        
            case 'subtask_added':
                $decodedChanges = json_decode($this->getRawOriginal('changes'), true);
                $parentTaskName = $decodedChanges['parent_task_name'] ?? 'unknown task';
                // Check if this is a subtask by verifying parent_task_name exists
                if (isset($decodedChanges['parent_task_name'])) {
                    return "<strong>{$userName}</strong> added subtask \"{$taskName}\" to task \"{$parentTaskName}\"";
                }
                return "<strong>{$userName}</strong> created task \"{$taskName}\"";
        
            // Replace the existing subtask_updated case in the getFormattedMessage() method:
            case 'subtask_updated':
                $decodedChanges = json_decode($this->getRawOriginal('changes'), true);
                $parentTaskName = $decodedChanges['parent_task_name'] ?? 'unknown task';
                $messages = [];

                // Format each changed field
                if (isset($decodedChanges['task_name'])) {
                    $messages[] = "<strong>{$userName}</strong> renamed subtask \"{$decodedChanges['task_name']['old']}\" to \"{$decodedChanges['task_name']['new']}\" in task \"{$parentTaskName}\"";
                }
                
                if (isset($decodedChanges['start_date'])) {
                    $messages[] = "<strong>{$userName}</strong> changed start date of subtask \"{$taskName}\" from \"{$decodedChanges['start_date']['old']}\" to \"{$decodedChanges['start_date']['new']}\"";
                }
                
                if (isset($decodedChanges['due_date'])) {
                    $messages[] = "<strong>{$userName}</strong> changed due date of subtask \"{$taskName}\" from \"{$decodedChanges['due_date']['old']}\" to \"{$decodedChanges['due_date']['new']}\"";
                }
                
                if (isset($decodedChanges['priority'])) {
                    $messages[] = "<strong>{$userName}</strong> changed priority of subtask \"{$taskName}\" from \"{$decodedChanges['priority']['old']}\" to \"{$decodedChanges['priority']['new']}\"";
                }
                
                if (isset($decodedChanges['status'])) {
                    $messages[] = "<strong>{$userName}</strong> changed status of subtask \"{$taskName}\" from \"{$decodedChanges['status']['old']}\" to \"{$decodedChanges['status']['new']}\"";
                }
                
                if (isset($decodedChanges['budget'])) {
                    $oldBudget = number_format($decodedChanges['budget']['old'], 2);
                    $newBudget = number_format($decodedChanges['budget']['new'], 2);
                    $messages[] = "<strong>{$userName}</strong> changed budget of subtask \"{$taskName}\" from \"₱{$oldBudget}\" to \"₱{$newBudget}\"";
                }
                
                if (isset($decodedChanges['source_of_funding'])) {
                    $messages[] = "<strong>{$userName}</strong> changed funding source of subtask \"{$taskName}\" from \"{$decodedChanges['source_of_funding']['old']}\" to \"{$decodedChanges['source_of_funding']['new']}\"";
                }
                
                return !empty($messages) ? implode("<br>", $messages) : "<strong>{$userName}</strong> updated subtask \"{$taskName}\" in task \"{$parentTaskName}\"";
            case 'subtask_deleted':
                $decodedChanges = json_decode($this->getRawOriginal('changes'), true);
                $parentTaskName = $decodedChanges['parent_task_name'] ?? 'unknown task';
                return "<strong>{$userName}</strong> deleted subtask \"{$taskName}\" from task \"{$parentTaskName}\"";
                
            case 'deleted':
                return "<strong>{$userName}</strong> deleted task \"{$taskName}\"";
                
            default:
                return "<strong>{$userName}</strong> performed an action on task \"{$taskName}\"";
        }
    }
}
