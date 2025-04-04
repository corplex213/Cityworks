<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DetailController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'text' => 'required|string',
            'key_persons' => 'nullable|string',
            'status' => 'required|string',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'comments' => 'nullable|string',
            'budget' => 'nullable|numeric',
            'file_upload' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        // Handle file upload if present
        if ($request->hasFile('file_upload')) {
            $validated['file_upload'] = $request->file('file_upload')->store('uploads', 'public');
        }

        // Save the data to the database
        ProjectDetail::create($validated);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Project details saved successfully!');
    }
}
