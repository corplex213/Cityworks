<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function show($id)
    {
        $project = Project::findOrFail($id);
        $users = User::all();
        return view('project-management', compact('project', 'users'));
    }
    
    // Show all projects
    public function listProjects(Request $request)
    {
        $query = Project::where('archived', false);

        // Check if a search query is provided
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('proj_name', 'like', '%' . $search . '%')
                ->orWhere('location', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
            });
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
            'location' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Add the default status
        $data = $request->all();
        $data['status'] = 'In Progress';

        Project::create($data);

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
            'status' => $project->status,
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
    public function updateStatus(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:In Progress,Completed,Delayed', // Validate status
        ]);

        $project->update([
            'status' => $request->status,
        ]);

        return redirect()->route('projects')->with('success', 'Project status updated successfully.');
    }
    
}
