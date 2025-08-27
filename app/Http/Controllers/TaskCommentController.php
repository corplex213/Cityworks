<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class TaskCommentController extends Controller
{
    protected $notificationService;

    public function attachments()
    {
        return $this->hasMany(\App\Models\TaskCommentAttachment::class);
    }

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('permission:comment on tasks')->only([
            'addComment', 'updateComment', 'deleteComment'
        ]);
    }

    public function getTaskComments($task)
    {
        $task = Task::findOrFail($task);

        $parentComments = $task->comments()
            ->whereNull('parent_id')
            ->with(['user:id,name,email', 'replies.user:id,name,email'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($comment) {
                $comment->edited = $comment->edited_at !== null;
                $comment->replies->each(function ($reply) {
                    $reply->edited = $reply->edited_at !== null;
                });
                return $comment;
            });

        return response()->json($parentComments);
    }

    public function addComment(Request $request, $task)
    {
        $task = Task::findOrFail($task);
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:task_comments,id',
            'attachments.*' => 'file|max:10240'
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('comment_attachments', 'public');
                $attachments[] = [
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => Auth::id(),
                ];
            }
        }

        $comment = $task->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id,
            'attachments'=> !empty($attachments) ? $attachments : null,
        ]);
        activity('task')
            ->causedBy(auth()->user())
            ->performedOn($task)
            ->withProperties([
                'project_id' => $task->project_id,
                'comment_id' => $comment->id,
                'content' => $comment->content,
                'is_reply' => $request->parent_id ? true : false,
                'parent_comment_id' => $request->parent_id,
            ])
            ->log($request->parent_id ? 'Replied to comment' : 'Added comment');

        $comment->load(['user:id,name,email']);
        // Create notification for the task owner and assigned user
        $usersToNotify = collect();
        if ($task->project && $task->project->user) {
            $usersToNotify->push($task->project->user);
        }
        
        // Add assigned user if exists and is not the current user
        if ($task->assignedUser && $task->assignedUser->id !== Auth::id()) {
            $usersToNotify->push($task->assignedUser);
        }

        $this->processMentions($comment, $task, $usersToNotify);

        foreach ($usersToNotify as $user) {
            $this->notificationService->createForUser(
                $user->id,
                $request->parent_id ? 'comment_reply' : 'comment_added',
                $request->parent_id ? 'New Reply to Your Comment' : 'New Comment on Task',
                $request->parent_id 
                    ? Auth::user()->name . ' replied to your comment on task "' . $task->task_name . '"'
                    : Auth::user()->name . ' added a new comment on task "' . $task->task_name . '"',
                route('projects.show', $task->project_id)
            );
        }
        

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment
        ], 201);
    }

    protected function processMentions($comment, $task, $usersToNotify)
    {
        try {
            // Use a regex that specifically looks for our special format @[username]
            $mentionPattern = '/@\[([^\]]+)\]/';
            preg_match_all($mentionPattern, $comment->content, $matches);
            
            if (!empty($matches[1])) {
                foreach ($matches[1] as $mentionedName) {
                    $mentionedName = trim($mentionedName);
                    
                    
                    // Try to find the user by name
                    $mentionedUser = User::where('name', $mentionedName)->first();
                    
                    // If not found, try with case-insensitive search
                    if (!$mentionedUser) {
                        $mentionedUser = User::whereRaw('LOWER(name) = ?', [strtolower($mentionedName)])->first();
                    }
                    
                    if ($mentionedUser) {
                        if ($mentionedUser->id !== Auth::id()) {
                            $this->notificationService->createForUser(
                                $mentionedUser->id,
                                'mention',
                                'You were mentioned',
                                Auth::user()->name . ' mentioned you in a comment on task "' . $task->task_name . '"',
                                route('projects.show', $task->project_id)
                            );
                        }
                        activity('task')
                            ->causedBy(auth()->user())
                            ->performedOn($task)
                            ->withProperties([
                                'project_id' => $task->project_id,
                                'comment_id' => $comment->id,
                                'mentioned_user_id' => $mentionedUser->id,
                                'mentioned_user_name' => $mentionedUser->name,
                                'content' => $comment->content,
                            ])
                            ->log('Mentioned user: ' . $mentionedUser->name);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error processing mentions: ' . $e->getMessage());
        }
    }

    public function updateComment(Request $request, TaskComment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized. You can only edit your own comments.'
            ], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $oldContent = $comment->content;

        $comment->update([
            'content' => $request->content,
            'edited_at' => now()
        ]);

        activity('task')
            ->causedBy(auth()->user())
            ->performedOn($comment->task)
            ->withProperties([
                'project_id' => $comment->task->project_id,
                'comment_id' => $comment->id,
                'old_content' => $oldContent,
                'new_content' => $request->content,
            ])
            ->log('Edited comment');

        $comment->load('user:id,name,email');

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment
        ]);
    }

    public function deleteComment(TaskComment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized. You can only delete your own comments.'
            ], 403);
        }

        activity('task')
            ->causedBy(auth()->user())
            ->performedOn($comment->task)
            ->withProperties([
                'project_id' => $comment->task->project_id,
                'comment_id' => $comment->id,
                'content' => $comment->content,
            ])
            ->log('Deleted comment');

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }
} 