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
        // Get all users, then group by position (no pagination)
        $users = User::orderBy('position')->orderBy('name')->get();
        $usersByPosition = $users->groupBy('position');

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

        return view('personnel', compact('usersByPosition', 'tasks', 'hierarchy'));
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

        // Assign role based on position
        switch ($validated['position']) {
            case 'City Engineer':
            case 'Assistant City Engineer':
            case 'Supervising Administrative Officer':
            case 'Division Head':
                $user->assignRole('administrative');
                break;
            case 'Group Leaders':
                $user->assignRole('managerial');
                break;
            case 'Technical Personnel':
                $user->assignRole('staff');
                break;
            default:
                $user->assignRole('staff');
                break;
        }

        return redirect()->route('personnel')->with('success', 'User registered successfully!');
    }
}
