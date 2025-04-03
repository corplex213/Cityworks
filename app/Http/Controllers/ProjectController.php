<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // Show all projects
    public function index()
    {
        $projects = Project::where('archived', false)->get();
        return view('projects', compact('projects'));
    }
    public function show($id)
    {
        $project = Project::findOrFail($id); // Retrieve the project by ID
        return view('projectTemplate', compact('project')); // Pass project data to the view
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
    // Delete Project
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return redirect()->route('projects')->with('success', 'Project deleted successfully.');
    }
    public function showContent($id)
    {
    $project = Project::findOrFail($id);
    return view('project.template', compact('project'));
    }
}
