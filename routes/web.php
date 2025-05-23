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

// Password reset route 
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
Route::middleware('auth')->group(function () {
    Route::get('/adminEditProfile', [ProfileController::class, 'adminEditProfile'])->name('adminEdit');
    Route::post('/adminEditProfile', [ProfileController::class, 'adminUpdate'])->name('admin_profile.update');
});

// this for edit a profile for a technician
Route::middleware('auth')->group(function () {
    Route::get('/techEditProfile', [ProfileController::class, 'editProfile'])->name('tech_edit');
    Route::post('/techEditProfile', [ProfileController::class, 'techUpdate'])->name('tech_profile.update');
    Route::get('/techProfile', [ProfileController::class, 'techProfile'])->name('techProfile');
});


Route::get('/adminProfile', [ProfileController::class, 'adminProfile'])->name('adminProfile');
Route::get('/home', function () {
    return redirect()->route('Student.dashboard');
})->name('home');


//all issues related urls for the student
Route::middleware('auth')->group(function () {
    Route::post('/report-issue', [IssueController::class, 'store'])->name('issue.store');
    Route::post('/save-issue', [IssueController::class, 'save'])->name('issue.save');
    Route::get('/view-issues', [IssueController::class, 'viewAllIssues'])->name('Student.view_issues');
    Route::get('/view-issues/{id}', [IssueController::class, 'viewIssueDetails'])->name('Student.issue_details');
    Route::get('/report-issue', [IssueController::class, 'create'])->name('Student.createissue');
    Route::get('/issues/{issue}/edit', [IssueController::class, 'editReportedIssue'])->name('Student.editissue');
    Route::put('/issues/{issue}', [IssueController::class, 'update'])->name('Student.updateissue');
    Route::get('/report-issue/confirm', [IssueController::class, 'confirm'])->name('Student.confirmissue');
    Route::get('/student/issues/{issue}/edit', [IssueController::class, 'edit'])->name('Student.edit_issue');
    Route::put('/student/issues/{issue}', [IssueController::class, 'update'])->name('Student.update_issue');
    Route::get('/issue/success', [IssueController::class, 'success'])->name('issue.success');
    Route::get('/edit-issue', [IssueController::class, 'edit'])->name('issue.edit');

});

// Notification routes grouped under 'auth' middleware
Route::middleware('auth')->group(function () {
    // Notification index routes
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::get('/notifications/technician', [NotificationController::class, 'indexTechnician'])
        ->name('notification.index');

    // Mark all as read (optional)
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.markAllRead');

    // Show specific notifications
    Route::get('/notification/{notification}', [NotificationController::class, 'show'])
        ->name('notifications.show');
    Route::get('/Technician/notifications/{notification}', [NotificationController::class, 'showTechnician'])
        ->name('notifications.Techshow');

    // Delete notifications
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');
    Route::delete('/notifications/bulk-delete', [NotificationController::class, 'bulkDestroy'])
        ->name('notifications.bulkDestroy');
});



// Grouped routes under 'auth' middleware
Route::middleware('auth')->group(function () {
    // Route to view completed tasks
    Route::get('/completed-tasks', [TaskController::class, 'completedTasks'])->name('completed.tasks');
    
    // Route to view tasks updates
    Route::get('/tasks/{task_id}/updates', [TaskController::class, 'taskUpdates'])->name('tasks.updates');
    
    // Admin route to view tasks
    Route::get('/admin/tasks/view', [TaskController::class, 'viewTasks'])->name('admin.tasks.view');
    
    // Route to view task progress
    Route::get('/tasks/{task}/progress', [TaskAssignmentController::class, 'show'])->name('tasks.progress.show');
Route::get('/assigned-tasks', [TaskController::class, 'assignedTasks'])->name('Assigned_tasks');
Route::get('/technician/tasks/{task_id}', [TechnicianController::class, 'viewTaskDetails'])->name('technician.task_details');
Route::get('/technician/directions', [TechnicianController::class, 'directions'])->name('technician.directions');


// Route to display the update form
Route::get('/tasks/update/{task_id}', [TaskController::class, 'showUpdateForm'])->name('tasks.update.form');

// Route to handle the update submission
Route::put('/tasks/update/{task_id}', [TaskController::class, 'updateTask'])->name('tasks.update');



});


    // Location Management Routes
Route::resource('locations', LocationQrController::class)->except(['show']);   
Route::get('locations', [LocationQRController::class, 'index'])->name('admin.locations.index');
Route::post('locations', [LocationQrController::class, 'store'])->name('admin.locations.store');
Route::get('locations/{location}/edit', [LocationQrController::class, 'edit'])->name('admin.locations.edit');
Route::delete('locations/{location}', [LocationQrController::class, 'destroy'])->name('admin.locations.destroy');
Route::put('locations/{location}', [LocationQrController::class, 'update'])->name('admin.locations.update');


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

        Route::get('/admin/reports/technician-performance/export/pdf', [ReportController::class, 'exportPdf'])->name('admin.report.export.pdf');
Route::get('/admin/reports/technician-performance/export/excel', [ReportController::class, 'exportExcel'])->name('admin.report.export.excel');


Route::post('/issues/{issue}/feedback', [FeedbackController::class, 'store'])
    ->name('feedback.submit')
    ->middleware('auth');


    Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');
