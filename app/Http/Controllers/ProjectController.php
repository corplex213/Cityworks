<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Show all projects
    public function index()
    {
        $projects = Project::all();
        return view('projects', compact('projects'));
    }

    // Store new project
    public function store(Request $request)
    {
        $request->validate([
            'proj_name' => 'required|string|max:255',
            'location' => 'required|string',
            'description' => 'required|string',
        ]);

        Project::create($request->all());

        return redirect()->route('projects')->with('success', 'Project created successfully.');
    }

    // Show edit form
    public function edit($id)
    {
        $project = Project::findOrFail($id);
        return view('edit_project', compact('project'));
    }

    // Update Project
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
    
        $request->validate([
            'proj_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
    
        $project->update([
            'proj_name' => $request->proj_name,
            'location' => $request->location,
            'description' => $request->description,
        ]);
    
        return redirect()->route('projects')->with('success', 'Project updated successfully.');
    }
    // Archive Project
    public function archive($id)
    {
        $project = Project::findOrFail($id);
        $project->update(['archived' => true]);

        return redirect()->route('projects')->with('success', 'Project archived successfully.');
    }

    // Delete Project
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return redirect()->route('projects')->with('success', 'Project deleted successfully.');
    }
}
