<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Mail\ProjectAssignedMail;
use Illuminate\Support\Facades\Mail;

class ProjectController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;

        $this->middleware('permission:view projects')->only(['show', 'listProjects']);
        $this->middleware('permission:create projects')->only(['store']);
        $this->middleware('permission:edit projects')->only(['edit', 'update', 'updateStatus']);
        $this->middleware('permission:delete projects')->only(['destroy', 'bulkDelete']);
        $this->middleware('permission:archive projects')->only(['bulkArchive']);
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);

        // Users with tasks for Kanban
        $userIdsWithTasks = \App\Models\Task::where('project_id', $project->id)
            ->whereNull('parent_task_id')
            ->pluck('assigned_to')
            ->unique()
            ->toArray();
        $users = \App\Models\User::whereIn('id', $userIdsWithTasks)->get();

        // All users for the modal
        $allUsers = \App\Models\User::all();

        return view('project-management', compact('project', 'users', 'allUsers'));
    }
    
    // Show all projects
    public function listProjects(Request $request)
    {
        $query = Project::where('archived', false);

        // Apply search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('proj_name', 'like', '%' . $search . '%')
                ->orWhere('proj_type', 'like', '%' . $search . '%')
                ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        // Apply project type filter (tab selection)
        if ($request->has('proj_type') && $request->proj_type != '') {
            $query->where('proj_type', $request->proj_type);
        }

        // Handle sorting
        $sortField = $request->get('sort', 'created_at'); // Default sort field
        $sortDirection = $request->get('direction', 'asc'); // Default sort direction
        $query->orderBy($sortField, $sortDirection);

        // Paginate the results
        $projects = $query->paginate(10);

        return view('projects', compact('projects', 'sortField', 'sortDirection'));
    }
    
    // Store new project
    public function store(Request $request)
    {
        $request->validate([
            'proj_name' => 'required|string|max:255',
            'proj_type' => 'required|string|in:POW,Investigation,MTS,Communication,R&D',
        ]);

        $project = Project::create([
            'proj_name' => $request->proj_name,
            'proj_type' => $request->proj_type,
            'status' => 'In Progress',
            'archived' => false,
        ]);

        // Create notification for all users about the new project
        $this->notificationService->createForAllUsers(
            'activity_created',
            'New Activity Created',
            'A new activity "' . $project->proj_name . '" has been created.',
            route('projects.show', $project->id)
        );

        return redirect()->route('projects.show', $project->id)->with('success', 'Activity created successfully.');
    }

    // Show edit form
    public function edit($id)
    {
        $this->authorize('edit projects');
        $project = Project::findOrFail($id);
        return view('edit_project', compact('project'));
    }
    
    // Update Project
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'proj_name' => 'required|string|max:255',
            'proj_type' => 'required|string|in:POW,Investigation,MTS,Communication,R&D',
        ]);

        $project->update([
            'proj_name' => $request->proj_name,
            'proj_type' => $request->proj_type,
            'status' => $project->status,
        ]);

        return redirect()->route('projects')->with('success', 'Activity updated successfully.');
    }
    // Delete Project
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return redirect()->route('projects')->with('success', 'Activity deleted successfully.');
    }
    public function updateStatus(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:In Progress,Completed,Deferred',
        ]);

        $oldStatus = $project->status;
        $project->update([
            'status' => $request->status,
        ]);

        // Create notification if status is changed to Completed
        if ($request->status === 'Completed' && $oldStatus !== 'Completed') {
            $this->notificationService->createForAllUsers(
                'activity_completed',
                'Activity Completed',
                'The activity "' . $project->proj_name . '" has been marked as completed.',
                route('projects.show', $project->id)
            );
        }

        return redirect()->route('projects')->with('success', 'Activity status updated successfully.');
    }
    public function bulkDelete(Request $request)
        {
        $ids = json_decode($request->input('ids'), true);

        if (is_array($ids) && count($ids) > 0) {
            // Delete each project individually to ensure events fire
            foreach ($ids as $id) {
                $project = Project::find($id);
                if ($project) {
                    $project->delete(); // This will trigger model events properly
                }
            }
            return redirect()->route('projects')->with('success', 'Selected activities have been deleted.');
        }

        return redirect()->route('projects')->with('error', 'No activities selected for deletion.');
    }
    public function bulkArchive(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:projects,id',
        ]);

        // Convert string IDs to array if they come as a comma-separated string
        $ids = is_string($request->ids) ? explode(',', $request->ids) : $request->ids;

        // Archive the selected projects
        Project::whereIn('id', $ids)->update(['archived' => true]);

        return redirect()->route('projects')->with('success', 'Selected activities archived successfully.');
    }
}
