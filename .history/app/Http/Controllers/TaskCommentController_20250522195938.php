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

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
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
            'parent_id' => 'nullable|exists:task_comments,id'
        ]);

        $comment = $task->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id
        ]);

        $comment->load('user:id,name,email');
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
            \Log::info('Processing mentions in comment: ' . $comment->content);
            
            // Modified regex to better handle multi-word usernames
            // This will match the longest possible string after @ until it hits a non-alphanumeric character
            // except spaces which are allowed within usernames
            $mentionPattern = '/@([a-zA-Z0-9 ]+)/';
            preg_match_all($mentionPattern, $comment->content, $matches);
            
            \Log::info('Full mention matches: ', $matches[1] ?? []);
            
            if (!empty($matches[1])) {
                foreach ($matches[1] as $mentionedName) {
                    $mentionedName = trim($mentionedName);
                    \Log::info('Looking for user with name: ' . $mentionedName);
                    
                    // Try to find the user by name
                    $mentionedUser = User::where('name', $mentionedName)->first();
                    
                    // If not found, try with case-insensitive search
                    if (!$mentionedUser) {
                        $mentionedUser = User::whereRaw('LOWER(name) = ?', [strtolower($mentionedName)])->first();
                        if ($mentionedUser) {
                            \Log::info('Found user with case-insensitive search: ' . $mentionedUser->name);
                        }
                    }
                    
                    if ($mentionedUser) {
                        \Log::info('Found mentioned user: ' . $mentionedUser->name);
                        
                        if ($mentionedUser->id !== Auth::id()) {
                            \Log::info('Creating mention notification for user: ' . $mentionedUser->name);
                            
                            $this->notificationService->createForUser(
                                $mentionedUser->id,
                                'mention',
                                'You were mentioned',
                                Auth::user()->name . ' mentioned you in a comment on task "' . $task->task_name . '"',
                                route('projects.show', $task->project_id)
                            );
                        }
                    } else {
                        \Log::info('No user found with name: ' . $mentionedName);
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

        $comment->update([
            'content' => $request->content,
            'edited_at' => now()
        ]);

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

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }
} 