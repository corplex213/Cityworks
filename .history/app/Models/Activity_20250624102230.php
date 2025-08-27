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
            'changes_raw' => $this->getRawOriginal('changes'),
            'changes_decoded' => json_decode($this->getRawOriginal('changes'), true),
        ]);

        $userName = $this->user->name;
        $decodedChanges = json_decode($this->getRawOriginal('changes'), true);

        // For subtask operations, use the task_name from changes if available
        if (in_array($this->type, ['subtask_updated', 'subtask_added', 'subtask_deleted'])) {
            if ($this->type === 'subtask_updated' && isset($decodedChanges['task_name']['new'])) {
                $taskName = $decodedChanges['task_name']['new'];
            } elseif ($this->task && $this->task->task_name) {
                $taskName = $this->task->task_name;
            } else {
                $taskName = isset($decodedChanges['task_name']) ? $decodedChanges['task_name'] : 'unknown subtask';
            }
        } else {
            $taskName = $this->task ? $this->task->task_name : 'unknown task';
        }

        switch ($this->type) {
            case 'added_engineer':
                $targetUserName = $decodedChanges['target_user_name'] ?? 'unknown user';
                return "<strong>{$userName}</strong> created task table for <strong>{$targetUserName}</strong>";
            
            case 'removed_engineer':
                $targetUserName = $decodedChanges['target_user_name'] ?? 'unknown user';
                return "<strong>{$userName}</strong> removed task table for <strong>{$targetUserName}</strong>";

            case 'created':
                $assignedUserId = $decodedChanges['assigned_to']['new'] ?? null;
                $targetUserName = User::find($assignedUserId)->name ?? 'unknown user';
                return "<strong>{$userName}</strong> created task \"{$taskName}\" assigned to <strong>{$targetUserName}</strong>";
        
            case 'updated':
                if (empty($decodedChanges)) {
                    return "<strong>{$userName}</strong> updated task \"{$taskName}\"";
                }
                $messages = [];
                foreach ($decodedChanges as $field => $change) {
                    if (!isset($change['old']) || !isset($change['new']) || $change['old'] === $change['new']) {
                        continue;
                    }
                    $oldValue = $change['old'];
                    $newValue = $change['new'];
                    $fieldDisplay = ucfirst(str_replace('_', ' ', $field));
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
                $parentTaskName = $decodedChanges['parent_task_name'] ?? 'unknown task';
                if (isset($decodedChanges['parent_task_name'])) {
                    return "<strong>{$userName}</strong> added subtask \"{$taskName}\" to task \"{$parentTaskName}\"";
                }
                return "<strong>{$userName}</strong> created task \"{$taskName}\"";
        
            case 'subtask_updated':
                \Log::debug('Formatting subtask_updated activity', [
                    'activity_id' => $this->id,
                    'changes' => $decodedChanges
                ]);
                $parentTaskName = $decodedChanges['parent_task_name'] ?? 'unknown task';
                foreach ($decodedChanges as $field => $change) {
                    if ($field === 'parent_task_name') continue;
                    $old = $change['old'] ?? 'unknown';
                    $new = $change['new'] ?? 'unknown';
                    switch ($field) {
                        case 'task_name':
                            return "<strong>{$userName}</strong> changed Task name from \"{$old}\" to \"{$new}\" in task \"{$parentTaskName}\"";
                        case 'start_date':
                            return "<strong>{$userName}</strong> changed start date of subtask \"{$taskName}\" from \"{$old}\" to \"{$new}\"";
                        case 'due_date':
                            if (!empty($old) && !empty($new)) {
                                $old = date('M d, Y', strtotime($old));
                                $new = date('M d, Y', strtotime($new));
                            }
                            return "<strong>{$userName}</strong> changed due date of subtask \"{$taskName}\" from \"{$old}\" to \"{$new}\"";
                        case 'priority':
                            return "<strong>{$userName}</strong> changed priority of subtask \"{$taskName}\" from \"{$old}\" to \"{$new}\"";
                        case 'status':
                            return "<strong>{$userName}</strong> changed status of subtask \"{$taskName}\" from \"{$old}\" to \"{$new}\"";
                        case 'budget':
                            $oldFormatted = number_format($old, 2);
                            $newFormatted = number_format($new, 2);
                            return "<strong>{$userName}</strong> changed budget of subtask \"{$taskName}\" from \"₱{$oldFormatted}\" to \"₱{$newFormatted}\"";
                        case 'source_of_funding':
                            return "<strong>{$userName}</strong> changed funding source of subtask \"{$taskName}\" from \"{$old}\" to \"{$new}\"";
                        case 'other_funding_source':
                            return "<strong>{$userName}</strong> changed other funding source of subtask \"{$taskName}\" from \"{$old}\" to \"{$new}\"";
                        default:
                            return "<strong>{$userName}</strong> updated subtask \"{$taskName}\" in task \"{$parentTaskName}\"";
                    }
                }
                return "<strong>{$userName}</strong> updated subtask in task \"{$parentTaskName}\"";
            case 'subtask_deleted':
                $parentTaskName = $decodedChanges['parent_task_name'] ?? 'unknown task';
                return "<strong>{$userName}</strong> deleted subtask \"{$taskName}\" from task \"{$parentTaskName}\"";
                
            case 'deleted':
                return "<strong>{$userName}</strong> deleted task \"{$taskName}\"";
                
            default:
                return "<strong>{$userName}</strong> performed an action on task \"{$taskName}\"";
        }
    }
}