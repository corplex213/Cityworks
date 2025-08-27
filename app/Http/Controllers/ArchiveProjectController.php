<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Spatie\Activitylog\Models\Activity;

class ArchiveProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view activities')->only(['listArchivedProjects']);
        $this->middleware('permission:archive activities')->only(['archive']);
        $this->middleware('permission:restore activities')->only(['restore', 'bulkRestore']);
    }

    // Show all archived projects
    public function listArchivedProjects(Request $request)
    {
        $archivedProjects = Project::query()->where('archived', true);

        // Apply search filter
        if ($search = $request->input('search')) {
            $archivedProjects->where('proj_name', 'like', "%{$search}%")
                             ->orWhere('status', 'like', "%{$search}%");
        }

        // Apply sorting
        if ($sort = $request->input('sort')) {
            $archivedProjects->orderBy($sort, 'asc');
        }

        $archivedProjects = $archivedProjects->paginate(10);

        return view('archiveProjects', [
            'archivedProjects' => $archivedProjects,
            'request' => $request, // Pass the request object explicitly
        ]);
    }

    // Archive a project
    public function archive($id)
    {
        $project = Project::findOrFail($id); 
        $project->update(['archived' => true]); 

        activity()
            ->causedBy(auth()->user())
            ->performedOn($project)
            ->log("Archived project: {$project->proj_name}");

        return redirect()->route('projects')->with('success', 'Activity archived successfully.');
    }

    // Restore an archived project
    public function restore($id)
    {
        $project = Project::findOrFail($id);
        $project->update(['archived' => false]);

        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->performedOn($project)
            ->log("Restored project: {$project->proj_name}");

        return redirect()->route('projects')->with('success', 'Activity restored successfully.');
    }

    public function bulkRestore(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:projects,id',
        ]);

        // Convert string IDs to array if they come as a comma-separated string
        $ids = is_string($request->ids) ? explode(',', $request->ids) : $request->ids;

        // Restore the selected projects and log each
        $projects = Project::whereIn('id', $ids)->get();
        foreach ($projects as $project) {
            $project->update(['archived' => false]);
            activity()
                ->causedBy(auth()->user())
                ->performedOn($project)
                ->log("Restored project: {$project->proj_name}");
        }

        return redirect()->route('archiveProjects')->with('success', 'Selected activity restored successfully.');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        // Log activity before deleting
        activity()
            ->causedBy(auth()->user())
            ->performedOn($project)
            ->log("Deleted archived project: {$project->proj_name}");

        $project->delete();

        return redirect()->route('archiveProjects')->with('success', 'Archived project deleted successfully.');
    }
    
}