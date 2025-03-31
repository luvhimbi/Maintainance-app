<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\notificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\LocationQrController;
use App\Http\Controllers\Admin\TechnicianControllers;
use App\Http\Controllers\Admin\TaskAssignmentController; 
use App\Http\Controllers\Admin\ReportController;
// Show login form and handle submissions
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Password reset route (optional)
Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset.form');
Route::post('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.update');

// guarded  routes for authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('Student.dashboard');
    Route::get('/technician/dashboard', [TechnicianController::class, 'dashboard'])->name('technician.dashboard');
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

// profile update routes for students
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
   Route::get('/test-profile', [ProfileController::class, 'edit'])->name('test.profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
});
//this is a profile for technician

Route::get('/techProfile', [ProfileController::class, 'editProfile'])->name('tech_edit');
Route::get('/techProfile', [ProfileController::class, 'techProfile'])->name('techProfile');
Route::get('/adminProfile', [ProfileController::class, 'adminProfile'])->name('adminProfile');
Route::get('/home', function () {
    return redirect()->route('Student.dashboard');
})->name('home');
//all issues related urls for the student
Route::post('/report-issue', [IssueController::class, 'store'])->name('issue.store');
Route::post('/save-issue', [IssueController::class, 'save'])->name('issue.save');
Route::get('/view-issues', [IssueController::class, 'viewAllIssues'])->name('Student.view_issues');
Route::get('/view-issues/{id}', [IssueController::class, 'viewIssueDetails'])->name('Student.issue_details');
Route::get('/report-issue', [IssueController::class, 'create'])->name('Student.createissue');
Route::get('/report-issue/confirm', [IssueController::class, 'confirm'])->name('Student.confirmissue');



Route::get('/issue/success', [IssueController::class, 'success'])->name('issue.success');
Route::get('/notifications', [NotificationController::class, 'index'])
    ->name('notifications.index')
    ->middleware('auth');

// Mark all as read (optional)
Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
    ->name('notifications.markAllRead')
    ->middleware('auth');

Route::get('/assigned-tasks', [TaskController::class, 'assignedTasks'])->name('Assigned_tasks');
Route::get('/technician/tasks/{task_id}', [TechnicianController::class, 'viewTaskDetails'])->name('technician.task_details');


Route::get('/edit-issue', [IssueController::class, 'edit'])->name('issue.edit');

// Route to display the update form
Route::get('/tasks/update/{task_id}', [TaskController::class, 'showUpdateForm'])->name('tasks.update.form');

// Route to handle the update submission
Route::post('/tasks/update/{task_id}', [TaskController::class, 'updateTask'])->name('tasks.update');

Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');

// Route to view completed tasks
Route::get('/completed-tasks', [TaskController::class, 'completedTasks'])->name('completed.tasks');
// Route to view tasks updates
Route::get('/tasks/{task_id}/updates', [TaskController::class, 'taskUpdates'])->name('tasks.updates');

Route::get('admin/tasks/assign', [TaskAssignmentController::class, 'create'])->name('tasks.assign');
Route::post('admin/tasks/assign', [TaskAssignmentController::class, 'store'])->name('tasks.store');
Route::get('/admin/tasks/view', [TaskController::class, 'viewTasks'])->name('admin.tasks.view');

    // Location Management Routes
Route::resource('locations', LocationQrController::class)->except(['show']);
    
Route::get('locations', [LocationQRController::class, 'index'])->name('admin.locations.index');
Route::get('locations/create', [LocationQrController::class, 'create'])->name('admin.locations.create');
Route::post('locations', [LocationQrController::class, 'store'])->name('admin.locations.store');
 Route::get('locations/{location}/edit', [LocationQrController::class, 'edit'])->name('admin.locations.edit');
Route::delete('locations/{location}', [LocationQrController::class, 'destroy'])->name('admin.locations.destroy');
Route::put('locations', [LocationQrController::class, 'update'])->name('admin.locations.update');



    // Route::get('/tasks/assign', [TaskController::class, 'showAssignForm'])->name('tasks.assign');
    // Route::get('/tasks/{task}/progress', [TaskController::class, 'showProgress'])->name('tasks.progress');
    // Route::get('/tasks/{task}/reassign', [TaskController::class, 'showReassignForm'])->name('tasks.reassign');
 Route::prefix('admin')->group(function() {
        Route::resource('students', \App\Http\Controllers\Admin\StudentController::class)
            ->names([
                'index' => 'admin.students.index',
                'create' => 'admin.students.create',
                'store' => 'admin.students.store',
                'edit' => 'admin.students.edit',
                'update' => 'admin.students.update',
                'destroy' => 'admin.students.destroy'
            ]);
});

 Route::resource('technicians', TechnicianControllers::class)
            ->names([
                'index' => 'admin.technicians.index',
                'create' => 'admin.technicians.create',
                'store' => 'admin.technicians.store',
                'show' => 'admin.technicians.show',
                'edit' => 'admin.technicians.edit',
                'update' => 'admin.technicians.update',
                'destroy' => 'admin.technicians.destroy'
        ]);
        Route::get('/reports', [ReportController::class, 'technicianPerformance'])
        ->name('admin.reports.technician-performance');