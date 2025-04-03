<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class ArchiveProjectController extends Controller
{
    // Show all archived projects
    public function index()
    {
        $archivedProjects = Project::where('archived', true)->get();
        return view('archiveProjects', compact('archivedProjects'));
    }

    // Archive a project
    public function archive($id)
    {
        $project = Project::findOrFail($id);
        \Log::info('Archiving project', ['project' => $project]);
        $project->update(['archived' => true]);
        \Log::info('Project archived', ['project_id' => $project->id]);

        return redirect()->route('projects')->with('success', 'Project archived successfully.');
    }

    // Restore an archived project
    public function restore($id)
    {
        $project = Project::findOrFail($id);
        $project->update(['archived' => false]);

        return redirect()->route('archiveProjects')->with('success', 'Project restored successfully.');
    }
}