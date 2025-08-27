<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Activity;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
    // Get total counts (all time)
    $totalProjects = Project::count();
    $inProgressProjects = Project::where('status', 'In Progress')->count();
    $completedProjects = Project::where('status', 'Completed')->count();
    $deferredProjects = Project::where('status', 'Deferred')->count();
    $allProjects = Project::select('status', 'proj_type', 'created_at')->get();

    // Define week ranges
    $now = Carbon::now();
    $startOfThisWeek = $now->copy()->startOfWeek();
    $endOfThisWeek = $now->copy()->endOfWeek();
    $startOfLastWeek = $now->copy()->subWeek()->startOfWeek();
    $endOfLastWeek = $now->copy()->subWeek()->endOfWeek();

    // Projects created this week
    $thisWeekTotal = Project::whereBetween('created_at', [$startOfThisWeek, $endOfThisWeek])->count();
    $thisWeekInProgress = Project::where('status', 'In Progress')->whereBetween('created_at', [$startOfThisWeek, $endOfThisWeek])->count();
    $thisWeekCompleted = Project::where('status', 'Completed')->whereBetween('created_at', [$startOfThisWeek, $endOfThisWeek])->count();
    $thisWeekDeferred = Project::where('status', 'Deferred')->whereBetween('created_at', [$startOfThisWeek, $endOfThisWeek])->count();

    // Projects created last week
    $lastWeekTotal = Project::whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();
    $lastWeekInProgress = Project::where('status', 'In Progress')->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();
    $lastWeekCompleted = Project::where('status', 'Completed')->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();
    $lastWeekDeferred = Project::where('status', 'Deferred')->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();

        
    // Calculate trends
    function calcTrend($current, $last) {
        if ($last == 0) return $current > 0 ? 100 : 0;
        $trend = round((($current - $last) / $last) * 100, 1);
        // Cap the trend between -100 and 100
        if ($trend > 100) return 100;
        if ($trend < -100) return -100;
        return $trend;
    }
    $totalProjectsTrend = calcTrend($thisWeekTotal, $lastWeekTotal);
    $inProgressTrend = calcTrend($thisWeekInProgress, $lastWeekInProgress);
    $completedTrend = calcTrend($thisWeekCompleted, $lastWeekCompleted);
    $deferredTrend = calcTrend($thisWeekDeferred, $lastWeekDeferred);
        

    // Task Completion Rate & Overdue Rate
    $totalTasks = Task::count();
    $completedOnTime = Task::where('status', 'Completed')
        ->whereColumn('completion_time', '<=', 'due_date')
        ->count();
    $overdueCompleted = Task::where('status', 'Completed')
        ->whereColumn('completion_time', '>', 'due_date')
        ->count();
    $overdueUnfinished = Task::where('status', '!=', 'Completed')
        ->where('due_date', '<', now())
        ->count();

    $completionRate = $totalTasks > 0 ? round(($completedOnTime / $totalTasks) * 100, 1) : 0;
    $overdueRate = $totalTasks > 0 ? round((($overdueCompleted + $overdueUnfinished) / $totalTasks) * 100, 1) : 0;

    // Average Time to Complete Tasks (in hours)
    $completedTasks = Task::where('status', 'Completed')
        ->whereNotNull('completion_time')
        ->whereNotNull('created_at')
        ->get();

    if ($completedTasks->count() > 0) {
        $totalTime = $completedTasks->reduce(function ($carry, $task) {
            $start = \Carbon\Carbon::parse($task->created_at);
            $end = \Carbon\Carbon::parse($task->completion_time);
            return $carry + $end->diffInSeconds($start);
        }, 0);
        $averageTimeSeconds = $totalTime / $completedTasks->count();
        $averageTimeHours = round($averageTimeSeconds / 3600, 2); // in hours
    } else {
        $averageTimeHours = 0;
    }

    // Overdue tasks: not completed and due_date < now
    $overdueTasks = Task::where('status', '!=', 'Completed')
        ->where('due_date', '<', now()->toDateString())
        ->orderBy('due_date', 'asc')
        ->take(5)
        ->get();

    // Subtask Analytics
    $mainTasks = Task::whereNull('parent_task_id')->get();
    $totalMainTasks = $mainTasks->count();
    $totalSubtasks = Task::whereNotNull('parent_task_id')->count();
    $averageSubtasks = $totalMainTasks > 0 ? round($totalSubtasks / $totalMainTasks, 2) : 0;

    // Subtask completion rate
    $completedSubtasks = Task::whereNotNull('parent_task_id')->where('status', 'Completed')->count();
    $subtaskCompletionRate = $totalSubtasks > 0 ? round(($completedSubtasks / $totalSubtasks) * 100, 1) : 0;

    // Optionally, get top 5 main tasks with most subtasks
    $topTasksWithSubtasks = Task::whereNull('parent_task_id')
        ->withCount('subtasks')
        ->orderByDesc('subtasks_count')
        ->take(5)
        ->get(['id', 'task_name', 'subtasks_count']);   

    // Task Creation & Completion Trends (by week)
    $taskTrends = [];
    $createdTasks = Task::selectRaw("YEARWEEK(created_at, 1) as week, COUNT(*) as created_count")
        ->groupBy('week')
        ->orderBy('week')
        ->get()
        ->keyBy('week');

    $completedTasks = Task::whereNotNull('completion_time')
        ->selectRaw("YEARWEEK(completion_time, 1) as week, COUNT(*) as completed_count")
        ->groupBy('week')
        ->orderBy('week')
        ->get()
        ->keyBy('week');

    // Merge weeks and prepare data for chart
    $allWeeks = collect($createdTasks->keys())->merge($completedTasks->keys())->unique()->sort();
    foreach ($allWeeks as $week) {
        $taskTrends[] = [
            'week' => $week,
            'created' => $createdTasks[$week]->created_count ?? 0,
            'completed' => $completedTasks[$week]->completed_count ?? 0,
        ];
    }
    // Aging Tasks: Not completed, ordered by start_date or created_at ascending
    $agingTasks = Task::where('status', '!=', 'Completed')
        ->orderByRaw('COALESCE(start_date, created_at) ASC')
        ->take(5)
        ->get();

        // Get project type distribution
        $projectTypes = Project::select('proj_type', \DB::raw('count(*) as count'))
            ->groupBy('proj_type')
            ->get()
            ->pluck('count', 'proj_type')
            ->toArray();


        // Historical: Group by week for priorities
        $priorityHistory = Task::selectRaw("YEARWEEK(created_at, 1) as week, priority, COUNT(*) as count")
            ->groupBy('week', 'priority')
            ->orderBy('week')
            ->get()
            ->groupBy('week');

        // Historical: Group by week for statuses
        $statusHistory = Task::selectRaw("YEARWEEK(created_at, 1) as week, status, COUNT(*) as count")
            ->groupBy('week', 'status')
            ->orderBy('week')
            ->get()
            ->groupBy('week');

        $userAssignment = Task::select('assigned_to', DB::raw('count(*) as count'))
        ->with('assignee') // Make sure you have a relation: public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }
        ->groupBy('assigned_to')
        ->get()
        ->map(function($item) {
            return [
                'user' => $item->assignee ? $item->assignee->name : 'Unassigned',
                'count' => $item->count
            ];
        });

        // Ensure all priority levels exist in the array
        $taskPriorities = Task::select('priority', \DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority')
            ->toArray();
        $allPriorities = ['High', 'Normal', 'Low'];
        foreach ($allPriorities as $priority) {
            if (!isset($taskPriorities[$priority])) {
                $taskPriorities[$priority] = 0;
            }
        }

        // Ensure all statuses exist in the array
        $taskStatuses = Task::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
        $allStatuses = ['Completed', 'For Checking', 'For Revision', 'Deferred'];
        foreach ($allStatuses as $status) {
            if (!isset($taskStatuses[$status])) {
                $taskStatuses[$status] = 0;
            }
        }

        // Get recent activities
        $recentActivities = Activity::with(['user', 'project', 'task'])
            ->latest()
            ->take(5)
            ->get();

        // Get upcoming deadlines
        $upcomingDeadlines = Task::select('tasks.*', 'projects.proj_name')
            ->join('projects', 'tasks.project_id', '=', 'projects.id')
            ->where('tasks.status', '!=', 'Completed')
            ->where('tasks.due_date', '>=', now())
            ->orderBy('tasks.due_date', 'asc')
            ->take(5)
            ->get();

        // Get all projects with their tasks
        $projectsWithTasks = Project::with('tasks')->get();

        // Prepare per-project task priorities and statuses
        $projectTaskData = [];
        foreach ($projectsWithTasks as $project) {
            $priorities = ['High' => 0, 'Normal' => 0, 'Low' => 0];
            $statuses = ['Completed' => 0, 'For Checking' => 0, 'For Revision' => 0, 'Deferred' => 0];

            foreach ($project->tasks as $task) {
                if (isset($priorities[$task->priority])) {
                    $priorities[$task->priority]++;
                }
                if (isset($statuses[$task->status])) {
                    $statuses[$task->status]++;
                }
            }

            $projectTaskData[$project->id] = [
                'name' => $project->proj_name,
                'priorities' => $priorities,
                'statuses' => $statuses,
            ];
        }

        return view('dashboard', compact(
            'totalProjects',
            'inProgressProjects',
            'completedProjects',
            'deferredProjects',
            'projectTypes',
            'taskPriorities',
            'taskStatuses',
            'recentActivities',
            'upcomingDeadlines',
            'projectTaskData',
            'totalProjectsTrend',
            'inProgressTrend',
            'completedTrend',
            'deferredTrend',
            'allProjects',
            'priorityHistory',
            'statusHistory',
            'userAssignment',
            'now',
            'startOfThisWeek',
            'endOfThisWeek',
            'startOfLastWeek',
            'endOfLastWeek',
            'thisWeekTotal',
            'lastWeekTotal','completionRate',
            'overdueRate',
            'completionRate',
            'overdueRate',
            'averageTimeHours',
            'overdueTasks',
            'totalSubtasks',
            'averageSubtasks',
            'subtaskCompletionRate',
            'topTasksWithSubtasks',
            'taskTrends',
            'agingTasks',
        ))->with('projects', Project::select('proj_name', 'created_at')->orderBy('created_at')->get()->toArray());;
    }
}