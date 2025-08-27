<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ArchiveProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view projects')->only(['listArchivedProjects']);
        $this->middleware('permission:archive projects')->only(['archive']);
        $this->middleware('permission:restore projects')->only(['restore', 'bulkRestore']);
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
        \Log::info('Archive request received for project ID: ' . $id);
        $project = Project::findOrFail($id); 
        $project->update(['archived' => true]); 

        return redirect()->route('projects')->with('success', 'Activity archived successfully.');
    }

    // Restore an archived project
    public function restore($id)
    {
        $project = Project::findOrFail($id);
        \Log::info('Restoring project ID: ' . $id);
        $project->update(['archived' => false]);

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

        // Restore the selected projects
        Project::whereIn('id', $ids)->update(['archived' => false]);

        return redirect()->route('archiveProjects')->with('success', 'Selected activity restored successfully.');
    }
}