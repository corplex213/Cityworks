<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;

class PersonnelController extends Controller
{
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
}
