<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectDetailController extends Controller
{
    // Display all project details
    public function index()
    {
        $projects = ProjectDetail::all();
        return view('projects.index', compact('projects'));
    }

    // Show the form for creating a new project
    public function create()
    {
        return view('projects.create');
    }

    // Store a new project in the database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:255',
            'key_persons' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'start_date' => 'required|date',
            'due_date' => 'required|date',
            'comments' => 'nullable|string',
            'file_upload' => 'nullable|string',
            'budget' => 'required|numeric',
        ]);

        ProjectDetail::create($validated);

        return redirect()->route('projects.index')->with('success', 'Project created successfully!');
    }
}
