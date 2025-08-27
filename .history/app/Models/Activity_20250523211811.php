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

        $userName = $this->user->name;
        $decodedChanges = json_decode($this->getRawOriginal('changes'), true);

        // For subtask operations, use the task_name from changes if available
        if (in_array($this->type, ['subtask_updated', 'subtask_added', 'subtask_deleted'])) {
            // For subtask_updated, use the new name if available
            if ($this->type === 'subtask_updated' && isset($decodedChanges['task_name']['new'])) {
                $taskName = $decodedChanges['task_name']['new'];
            } 
            // For subtask_added or when name wasn't changed, try to get from task relation
            elseif ($this->task && $this->task->task_name) {
                $taskName = $this->task->task_name;
            }
            // If no task relation or name isn't available, try to get from the raw value
            else {
                $taskName = isset($decodedChanges['task_name']) ? $decodedChanges['task_name'] : 'unknown subtask';
            }
        } else {
            $taskName = $this->task ? $this->task->task_name : 'unknown task';
        }

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
        
            case 'subtask_added':
                $decodedChanges = json_decode($this->getRawOriginal('changes'), true);
                $parentTaskName = $decodedChanges['parent_task_name'] ?? 'unknown task';
                // Check if this is a subtask by verifying parent_task_name exists
                if (isset($decodedChanges['parent_task_name'])) {
                    return "<strong>{$userName}</strong> added subtask \"{$taskName}\" to task \"{$parentTaskName}\"";
                }
                return "<strong>{$userName}</strong> created task \"{$taskName}\"";
        
            case 'subtask_updated':
                $decodedChanges = json_decode($this->getRawOriginal('changes'), true);
                $parentTaskName = $decodedChanges['parent_task_name'] ?? 'unknown task';
                $messages = [];

                // Format each changed field
                if (isset($decodedChanges['task_name'])) {
                    $oldName = $decodedChanges['task_name']['old'] ?? 'unknown';
                    $newName = $decodedChanges['task_name']['new'] ?? 'unknown';
                    $messages[] = "<strong>{$userName}</strong> renamed subtask \"{$oldName}\" to \"{$newName}\" in task \"{$parentTaskName}\"";
                }
                
                if (isset($decodedChanges['start_date'])) {
                    $oldDate = $decodedChanges['start_date']['old'] ?? '';
                    $newDate = $decodedChanges['start_date']['new'] ?? '';
                    $messages[] = "<strong>{$userName}</strong> changed start date of subtask \"{$taskName}\" from \"{$oldDate}\" to \"{$newDate}\"";
                }
                
                if (isset($decodedChanges['due_date'])) {
                    $oldDate = $decodedChanges['due_date']['old'] ?? '';
                    $newDate = $decodedChanges['due_date']['new'] ?? '';
                    $messages[] = "<strong>{$userName}</strong> changed due date of subtask \"{$taskName}\" from \"{$decodedChanges['due_date']['old']}\" to \"{$decodedChanges['due_date']['new']}\"";
                }
                
                if (isset($decodedChanges['priority'])) {
                        $oldPriority = $decodedChanges['priority']['old'] ?? 'None';
                        $newPriority = $decodedChanges['priority']['new'] ?? 'None';
                        if ($oldPriority !== $newPriority) {
                            $messages[] = "<strong>{$userName}</strong> changed priority of subtask \"{$taskName}\" from \"{$oldPriority}\" to \"{$newPriority}\"";
                        }
                    }

                    if (isset($decodedChanges['status'])) {
                        $oldStatus = $decodedChanges['status']['old'] ?? 'None';  
                        $newStatus = $decodedChanges['status']['new'] ?? 'None';
                        if ($oldStatus !== $newStatus) {
                            $messages[] = "<strong>{$userName}</strong> changed status of subtask \"{$taskName}\" from \"{$oldStatus}\" to \"{$newStatus}\"";
                        }
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
