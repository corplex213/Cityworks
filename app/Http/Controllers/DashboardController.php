<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class DashboardController extends Controller
{
    public function index()
    {
        // Query the database for project counts
        $totalProjects = Project::count();
        $inProgressProjects = Project::where('status', 'In Progress')->count();
        $completedProjects = Project::where('status', 'Completed')->count();
        $delayedProjects = Project::where('status', 'Delayed')->count();

        // Data for Project Progress Chart
        $projectProgressData = [
            'Completed' => $completedProjects,
            'In Progress' => $inProgressProjects,
            'Delayed' => $delayedProjects
        ];

         // Categorized projects
        $projectsByStatus = [
            'total' => Project::all(),
            'inProgress' => Project::where('status', 'In Progress')->get(),
            'completed' => Project::where('status', 'Completed')->get(),
            'delayed' => Project::where('status', 'Delayed')->get(),
        ];

        // Sample Dummy Data
        $data = [10, 20, 30, 40];
        $labels = ['January', 'February', 'March', 'April'];

        // Pass $projectProgressData to the view
        return view('dashboard', compact('data', 'labels', 'totalProjects', 'inProgressProjects', 'completedProjects', 'delayedProjects', 'projectProgressData', 'projectsByStatus'));
    }
}