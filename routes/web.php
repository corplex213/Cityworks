<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
//use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
    
//     /* I can't see this old dashboard thingy
//     return view('custom.old_dashboard');*/

//     /*so let's try this */
//     return view('dashboard');

// }) -> name('dashboard');
// //->middleware(['auth', 'verified'])->name('dashboard');


/*this is for authentication */
// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

/*but, for testing, i'll try to display the dashboard */
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

//Route::get('/', [LoginController::class, 'LoginPage'])->name('login'); for loginPage sana

// require __DIR__.'/auth.php';
