<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ArchiveProjectController;
use App\Http\Controllers\ProjectDetailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\TaskFileController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\TestMail;
use App\Http\Controllers\PowController;
use App\Http\Controllers\InvestigationController;
use App\Http\Controllers\MtsController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\RndController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\UserAccessController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CalendarEventController;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

Route::get('/projects/{project}/activities', function (\App\Models\Project $project) {
    $activities = Activity::whereHasMorph('subject', [\App\Models\Task::class], function ($query) use ($project) {
        $query->where('project_id', $project->id);
    })
    ->with('causer', 'subject')
    ->orderBy('created_at', 'desc')
    ->get();

    return response()->json($activities);
})->middleware(['auth', 'verified'])->name('projects.activities');

Route::put('/users/{user}', [UserAccessController::class, 'update'])->name('user.update');
Route::delete('/users/{user}', [UserAccessController::class, 'destroy'])->name('user.destroy');
Route::put('/users/{user}', [UserAccessController::class, 'update'])->name('user.update');
// Test route for email verification
Route::get('/test-email', function () {
    $user = User::first();
    if ($user) {
        $user->sendEmailVerificationNotification();
        return "Verification email sent to {$user->email}. Please check your inbox.";
    }
    return "No user found in the database.";
});

// Test route for sending a simple email
Route::get('/send-test-email', function () {
    $email = request('email', 'bonsi@gmail.com'); // Replace with your actual Gmail address
    Mail::to($email)->send(new TestMail());
    return "Test email sent to {$email}. Please check your inbox.";
});

//Route for landing page
Route::get('/', function () {
    return view('welcome');
});

//Route after logging in
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


//Route for Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/pow', [PowController::class, 'index'])->name('pow');
Route::get('/investigation', [InvestigationController::class, 'index'])->name('investigation');
Route::get('/mts', [MtsController::class, 'index'])->name('mts');
Route::get('/communication', [CommunicationController::class, 'index'])->name('communication');
Route::get('/rnd', [RndController::class, 'index'])->name('rnd');
Route::get('/personnel', [PersonnelController::class, 'index'])->name('personnel');

//Route for personnel
Route::post('/personnel/register', [PersonnelController::class, 'register'])->name('personnel.register');

//Route for Projects
Route::get('/projects', [ProjectController::class, 'listProjects'])->middleware(['auth', 'verified'])->name('projects');
Route::post('/projects', [ProjectController::class, 'store'])->middleware(['auth', 'verified'])->name('projects.store');
Route::get('/projects/{id}', [ProjectController::class, 'show'])->middleware(['auth', 'verified'])->name('projects.show');
Route::get('/projects/{id}/edit', [ProjectController::class, 'edit'])->middleware(['auth', 'verified'])->name('projects.edit');
Route::put('/projects/{id}', [ProjectController::class, 'update'])->middleware(['auth', 'verified'])->name('projects.update');
Route::put('/projects/{id}/archive', [ArchiveProjectController::class, 'archive'])->middleware(['auth', 'verified'])->name('projects.archive');
Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->middleware(['auth', 'verified'])->name('projects.destroy');
Route::put('/projects/{id}/status', [ProjectController::class, 'updateStatus'])->middleware(['auth', 'verified'])->name('projects.updateStatus');
Route::post('/projects/bulk-delete', [ProjectController::class, 'bulkDelete'])->middleware(['auth', 'verified'])->name('projects.bulkDelete');
Route::post('/projects/bulk-archive', [ProjectController::class, 'bulkArchive'])->middleware(['auth', 'verified'])->name('projects.bulkArchive');
Route::post('/projects/bulk-restore', [ArchiveProjectController::class, 'bulkRestore'])->middleware(['auth', 'verified'])->name('projects.bulkRestore');
Route::get('/archiveProjects', [ArchiveProjectController::class, 'listArchivedProjects'])->middleware(['auth', 'verified'])->name('archiveProjects');
Route::put('/projects/{id}/archive', [ArchiveProjectController::class, 'archive'])->middleware(['auth', 'verified'])->name('projects.archive');
Route::put('/projects/{id}/restore', [ArchiveProjectController::class, 'restore'])->middleware(['auth', 'verified'])->name('projects.restore');


//Route for profile
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
//Route Calendar
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
Route::get('/calendar/users', [CalendarController::class, 'getUsers']);
Route::delete('/calendar/events/{id}', [App\Http\Controllers\CalendarEventController::class, 'destroy'])->name('calendar.events.destroy');
//Route for Tasks
Route::post('/tasks', [TaskController::class, 'store'])->middleware(['auth', 'verified'])->name('tasks.store');
Route::put('/tasks/{id}', [TaskController::class, 'update'])->middleware(['auth', 'verified'])->name('tasks.update');
Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->middleware(['auth', 'verified'])->name('tasks.destroy');
Route::get('/projects/{projectId}/tasks', [TaskController::class, 'getProjectTasks'])->middleware(['auth', 'verified'])->name('projects.tasks');
Route::delete('/projects/{projectId}/users/{userId}/tasks', [TaskController::class, 'deleteUserTasks'])
    ->middleware(['auth', 'verified'])
    ->name('tasks.deleteUserTasks');
Route::get('/tasks/{task}/comments', [TaskCommentController::class, 'getTaskComments'])->middleware(['auth', 'verified'])->name('task.comments.index');
Route::get('/tasks/{task}', [TaskController::class, 'show']);
Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'addComment'])->middleware(['auth', 'verified'])->name('task.comments.store');
Route::put('/comments/{comment}', [TaskCommentController::class, 'updateComment'])->middleware(['auth', 'verified'])->name('task.comments.update');
Route::delete('/comments/{comment}', [TaskCommentController::class, 'deleteComment'])->middleware(['auth', 'verified'])->name('task.comments.destroy');
Route::post('/calendar/tasks', [TaskController::class, 'storeFromCalendar'])->middleware('auth');


// Routes for task completion timestamp
Route::get('/tasks/{task}/completion-time', [TaskController::class, 'getCompletionTime'])->middleware(['auth', 'verified'])->name('tasks.getCompletionTime');
Route::post('/tasks/{task}/mark-completed', [TaskController::class, 'markCompleted'])->middleware(['auth', 'verified'])->name('tasks.markCompleted');

// Task Files Routes
Route::get('/tasks/{task}/files', [TaskFileController::class, 'getTaskFiles'])->middleware(['auth', 'verified'])->name('task.files.index');
Route::post('/tasks/{task}/files', [TaskFileController::class, 'uploadFiles'])->middleware(['auth', 'verified'])->name('task.files.store');
Route::get('/files/{file}/download', [TaskFileController::class, 'downloadFile'])->middleware(['auth', 'verified'])->name('task.files.download');
Route::delete('/files/{file}', [TaskFileController::class, 'deleteFile'])->middleware(['auth', 'verified'])->name('task.files.destroy');

// Notification Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'deleteAll'])->name('notifications.deleteAll');
});

// Team Members API
Route::get('/api/team-members', function () {
    return App\Models\User::select('id', 'name', 'email')
        ->where('id', '!=', auth()->id())
        ->get();
})->middleware(['auth', 'verified']);

// Route for sorting view
Route::post('/save-sorting-view', [TaskController::class, 'saveSortingView'])->middleware(['auth', 'verified']);
Route::get('/get-sorting-view', [TaskController::class, 'getSortingView'])->middleware(['auth', 'verified'])->name('tasks.getSortingView');
Route::post('/reset-sorting-view', [TaskController::class, 'resetSortingView'])->middleware(['auth', 'verified'])->name('tasks.resetSortingView');

// Temporary route to assign admin role (remove this after use)
Route::get('/assign-admin-role', function () {
    $user = \App\Models\User::first(); // Get your user
    $user->assignRole('administrative');
    return 'Administrative role assigned to ' . $user->name;
})->middleware(['auth', 'verified']);

// User Access Control Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/user-access-control', [UserAccessController::class, 'index'])
        ->middleware(\App\Http\Middleware\CheckUserAccess::class . ':view user access control')
        ->name('userAccessControl');
    
    Route::post('/user-access-control/{user}/assign-role', [UserAccessController::class, 'assignRole'])
        ->middleware(\App\Http\Middleware\CheckUserAccess::class . ':manage user access')
        ->name('user-access.assign-role');
    
    Route::delete('/user-access-control/{user}/remove-role/{role}', [UserAccessController::class, 'removeRole'])
        ->middleware(\App\Http\Middleware\CheckUserAccess::class . ':manage user access')
        ->name('user-access.remove-role');
    
    Route::post('/user-access-control/{user}/assign-permission', [UserAccessController::class, 'assignPermission'])
        ->middleware(\App\Http\Middleware\CheckUserAccess::class . ':manage user access')
        ->name('user-access.assign-permission');
    
    Route::delete('/user-access-control/{user}/remove-permission/{permission}', [UserAccessController::class, 'removePermission'])
        ->middleware(\App\Http\Middleware\CheckUserAccess::class . ':manage user access')
        ->name('user-access.remove-permission');
    Route::post('/user-access/update-role-permissions', [UserAccessController::class, 'updateRolePermissions'])->name('user-access.update-role-permissions');
    Route::post('/calendar/events', [App\Http\Controllers\CalendarEventController::class, 'store']);
    Route::get('/calendar/calendar-events', [App\Http\Controllers\CalendarEventController::class, 'events']);


});


Route::post('/archiveProjects/bulk-delete', [ArchiveProjectController::class, 'bulkDelete'])
    ->middleware(['auth', 'verified'])
    ->name('archiveProjects.bulkDelete');


Route::get('/user-tasks', function () {
    $userId = Auth::id();
    $tasks = Task::with('project')
        ->where('assigned_to', $userId)
        ->get();
    return response()->json($tasks);
})->middleware(['auth', 'verified']);

require __DIR__.'/auth.php';
