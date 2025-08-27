<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public $inProgressProjects;
    public $completedProjects;
    public $deferredProjects;
    public $totalProjects;
    public $projectTypes;
    public $projectTaskData;
    public $allProjects;
    public $priorityHistory;
    public $statusHistory;
    public $userAssignment;
    public $taskTrends;
    public $recentActivities;
    public $upcomingDeadlines;
    public $overdueTasks;
    public $completionRate;
    public $overdueRate;
    public $averageTimeHours;
    public $agingTasks;
    public $totalSubtasks;
    public $averageSubtasks;
    public $subtaskCompletionRate;
    public $topTasksWithSubtasks;
    public $totalProjectsTrend;
    public $inProgressTrend;
    public $completedTrend;
    public $deferredTrend;

    public function mount(
        $inProgressProjects, $completedProjects, $deferredProjects, $totalProjects, $projectTypes, $projectTaskData, $allProjects, $priorityHistory, $statusHistory, $userAssignment, $taskTrends, $recentActivities, $upcomingDeadlines, $overdueTasks, $completionRate, $overdueRate, $averageTimeHours, $agingTasks, $totalSubtasks, $averageSubtasks, $subtaskCompletionRate, $topTasksWithSubtasks, $totalProjectsTrend, $inProgressTrend, $completedTrend, $deferredTrend
    ) {
        $this->inProgressProjects = $inProgressProjects;
        $this->completedProjects = $completedProjects;
        $this->deferredProjects = $deferredProjects;
        $this->totalProjects = $totalProjects;
        $this->projectTypes = $projectTypes;
        $this->projectTaskData = $projectTaskData;
        $this->allProjects = $allProjects;
        $this->priorityHistory = $priorityHistory;
        $this->statusHistory = $statusHistory;
        $this->userAssignment = $userAssignment;
        $this->taskTrends = $taskTrends;
        $this->recentActivities = $recentActivities;
        $this->upcomingDeadlines = $upcomingDeadlines;
        $this->overdueTasks = $overdueTasks;
        $this->completionRate = $completionRate;
        $this->overdueRate = $overdueRate;
        $this->averageTimeHours = $averageTimeHours;
        $this->agingTasks = $agingTasks;
        $this->totalSubtasks = $totalSubtasks;
        $this->averageSubtasks = $averageSubtasks;
        $this->subtaskCompletionRate = $subtaskCompletionRate;
        $this->topTasksWithSubtasks = $topTasksWithSubtasks;
        $this->totalProjectsTrend = $totalProjectsTrend;
        $this->inProgressTrend = $inProgressTrend;
        $this->completedTrend = $completedTrend;
        $this->deferredTrend = $deferredTrend;
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
