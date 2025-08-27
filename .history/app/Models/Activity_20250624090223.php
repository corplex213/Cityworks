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

    // Always get the task name from the relation or changes
    $taskName = $this->task ? $this->task->task_name : (
        isset($decodedChanges['task_name']['new']) ? $decodedChanges['task_name']['new'] :
        (isset($decodedChanges['task_name']) ? $decodedChanges['task_name'] : 'unknown task')
    );

    // Treat subtask actions as main task actions
    $type = $this->type;
    if ($type === 'subtask_added') $type = 'created';
    if ($type === 'subtask_updated') $type = 'updated';
    if ($type === 'subtask_deleted') $type = 'deleted';

    switch ($type) {
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

        case 'deleted':
            return "<strong>{$userName}</strong> deleted task \"{$taskName}\"";

        default:
            return "<strong>{$userName}</strong> performed an action on task \"{$taskName}\"";
    }
}
}
