<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class TaskFileController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('permission:upload files to tasks')->only(['uploadFiles', 'deleteFile']);
    }

    public function getTaskFiles(Task $task)
    {
        $files = $task->files()->with('user')->get();
        return response()->json($files);
    }

    public function uploadFiles(Request $request, Task $task)
    {
        $request->validate([
            'files.*' => 'required|file|max:10240', // 10MB max
        ]);

        $uploadedFiles = [];
        foreach ($request->file('files') as $file) {
            $path = $file->store('task-files/' . $task->id);
            
            $taskFile = $task->files()->create([
                'user_id' => auth()->id(),
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);

            $uploadedFiles[] = $taskFile->load('user');
            // --- Activity log for file upload ---
            activity('task')
                ->causedBy(auth()->user())
                ->performedOn($task)
                ->withProperties([
                    'project_id' => $task->project_id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_id' => $taskFile->id,
                ])
                ->log('Uploaded file: ' . $file->getClientOriginalName());
        }

        // Create notification for the task owner and assigned user
        $usersToNotify = collect();
        
        // Add project owner
        if ($task->project && $task->project->user) {
            $usersToNotify->push($task->project->user);
        }
        
        // Add assigned user if exists and is not the current user
        if ($task->assignedUser && $task->assignedUser->id !== Auth::id()) {
            $usersToNotify->push($task->assignedUser);
        }

        foreach ($usersToNotify as $user) {
            $this->notificationService->createForUser(
                $user->id,
                'file_uploaded',
                'New File Uploaded',
                Auth::user()->name . ' uploaded a file to task "' . $task->task_name . '"',
                route('projects.show', $task->project_id)
            );
        }

        return response()->json([
            'message' => 'Files uploaded successfully',
            'files' => $uploadedFiles
        ]);
    }

    public function downloadFile(TaskFile $file)
    {
        if (!Storage::exists($file->path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        return Storage::download($file->path, $file->name);
    }

    public function deleteFile(TaskFile $file = null)
    {
        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        if ($file->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // --- Activity log for file deletion ---
        activity('task')
            ->causedBy(auth()->user())
            ->performedOn($file->task)
            ->withProperties([
                'project_id' => $file->task->project_id,
                'file_id' => $file->id,
                'file_name' => $file->name,
            ])
            ->log('Deleted file: ' . $file->name);
        // --- end activity log ---

        Storage::delete($file->path);
        $file->delete();

        return response()->json(['message' => 'File deleted successfully']);
    }
} 