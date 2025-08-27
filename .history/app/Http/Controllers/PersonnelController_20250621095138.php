<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;

class PersonnelController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view user access control')->only(['index']);
        $this->middleware('permission:create users')->only(['register']);
    }
    public function index()
    {
        // Paginate users with 15 users per page within each position group
        $usersByPosition = User::orderBy('position')
        ->orderBy('name')
        ->paginate(15);
        
        // Group the paginated users by position
        $usersByPosition = $usersByPosition->groupBy('position');

        // Get all tasks with their projects and assigned users
        $tasks = Task::with(['project', 'assignedUser'])->get();
        
        // Define the organizational hierarchy
        $hierarchy = [
            'City Engineer' => [
                'Assistant City Engineer' => [
                    'Supervising Administrative Officer' => [
                        'Division Head' => [
                            'Group Leaders' => [
                                'Technical Personnel'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return view('personnel.index', compact('usersByPosition', 'tasks', 'hierarchy'));
    }
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'position' => 'required|string',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'position' => $validated['position'],
            'password' => bcrypt($validated['password']),
        ]);

        return redirect()->route('personnel')->with('success', 'User registered successfully!');
    }
}
