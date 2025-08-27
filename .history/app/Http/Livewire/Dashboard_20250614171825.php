<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\Task;
use App\Models\Activity;

class Dashboard extends Component
{
    public function render()
    {
        // Fetch all the data you need for the dashboard
        $totalProjects = Project::count();
        $inProgressProjects = Project::where('status', 'In Progress')->count();
        $completedProjects = Project::where('status', 'Completed')->count();
        $deferredProjects = Project::where('status', 'Deferred')->count();
        $totalProjectsTrend = 0; // Calculate as needed
        $inProgressTrend = 0; // Calculate as needed
        $completedTrend = 0; // Calculate as needed
        $deferredTrend = 0; // Calculate as needed
        $projectTypes = []; // Fill as needed
        $projectTaskData = []; // Fill as needed
        $cumulativeBudget = [];
        $cumulativeCompletedBudget = [];
        $cumulativeOngoingBudget = [];
        $plannedBudget = [];
        $actualBudget = [];
        $forecastBudget = [];
        $totalBudget = 0;
        $allProjects = Project::all();
        $priorityHistory = [];
        $statusHistory = [];
        $userAssignment = [];
        $upcomingDeadlines = []; // Fill as needed
        $recentActivities = Activity::latest()->take(5)->get();

        return view('livewire.dashboard', compact(
            'totalProjects',
            'inProgressProjects',
            'completedProjects',
            'deferredProjects',
            'totalProjectsTrend',
            'inProgressTrend',
            'completedTrend',
            'deferredTrend',
            'projectTypes',
            'projectTaskData',
            'cumulativeBudget',
            'cumulativeCompletedBudget',
            'cumulativeOngoingBudget',
            'plannedBudget',
            'actualBudget',
            'forecastBudget',
            'totalBudget',
            'allProjects',
            'priorityHistory',
            'statusHistory',
            'userAssignment',
            'upcomingDeadlines',
            'recentActivities'
        ));
    }
}
