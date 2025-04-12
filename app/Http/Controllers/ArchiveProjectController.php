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
            \Log::info('Archive request received for project ID: ' . $id);
            $project = Project::findOrFail($id); 
            $project->update(['archived' => true]); 

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