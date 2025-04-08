<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ArchiveProjectController;
use App\Http\Controllers\ProjectDetailController;
use Illuminate\Support\Facades\Route;

//Route for landing page
Route::get('/', function () {
    return view('welcome');
});
//Route after logging in
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//Route for Projects
Route::get('/projects', [ProjectController::class, 'listProjects'])->middleware(['auth'])->name('projects');
Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');
Route::get('/projects/{id}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
Route::put('/projects/{id}', [ProjectController::class, 'update'])->name('projects.update');
Route::put('/projects/{id}/archive', [ProjectController::class, 'archive'])->name('projects.archive');
Route::delete('/projects/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');
Route::put('/projects/{id}/status', [ProjectController::class, 'updateStatus'])->name('projects.updateStatus');



//Route for Archive Projects yet to be implemented
Route::get('/archiveProjects', [ArchiveProjectController::class, 'index'])->middleware(['auth'])->name('archiveProjects');
Route::put('/projects/{id}/archive', [ArchiveProjectController::class, 'archive'])->middleware(['auth'])->name('projects.archive');
Route::put('/projects/{id}/restore', [ArchiveProjectController::class, 'restore'])->middleware(['auth'])->name('projects.restore');

//Route for profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
