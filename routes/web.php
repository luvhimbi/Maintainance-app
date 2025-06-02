<?php

use App\Http\Controllers\FeedbackController;
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
use App\Http\Controllers\Admin\StaffController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports.index');


    Route::get('/admin/reports/students', [\App\Http\Controllers\Admin\ReportController::class, 'StudentsAndStaffReport'])->name('admin.reports.students_and_staff');
    Route::get('/admin/reports/students-staff/export-pdf', [ReportController::class, 'exportPdf'])->name('admin.reports.export_pdf');
    Route::get('/admin/reports/students-staff/export-excel', [ReportController::class, 'exportExcel'])->name('admin.reports.export_excel');
    Route::get('/admin/reports/students-staff/export-word', [ReportController::class, 'exportWord'])->name('admin.reports.export_word');

    Route::get('/admin/reports/tasks', [ReportController::class, 'MaintenanceTaskReport'])->name('admin.reports.tasks');
    Route::get('/tasks/export-pdf', [ReportController::class, 'exportTaskPdf'])->name('tasks.export.pdf');
    Route::get('/tasks/export-excel', [ReportController::class, 'exportTaskExcel'])->name('tasks.export.excel');
    Route::get('/tasks/export-word', [ReportController::class, 'exportTaskWord'])->name('tasks.export.word');


    Route::get('/admin/reports/technicians', [\App\Http\Controllers\Admin\ReportController::class, 'generateTechnicianReport'])->name('admin.reports.technicians');
    Route::get('/technicians/export-pdf', [ReportController::class, 'exportTechnicianPdf'])->name('technicians.export.pdf');
    Route::get('/technicians/export-excel', [ReportController::class, 'exportTechnicianExcel'])->name('technicians.export.excel');
    Route::get('/technicians/export-word', [ReportController::class, 'exportTechnicianWord'])->name('technicians.export.word');

    Route::get('/admin/reports/technician-performance/export/pdf', [ReportController::class, 'exportPdf'])->name('admin.report.export.pdf');
    Route::get('/admin/reports/technician-performance/export/excel', [ReportController::class, 'exportExcel'])->name('admin.report.export.excel');


});
// Show login form and handle submissions
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Password reset route
Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset.form');
Route::post('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.update');


// guarded  routes for authenticated users
Route::middleware('auth','prevent-back')->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('Student.dashboard');
    // Route::get('/technician/dashboard', [TechnicianController::class, 'dashboard'])->name('technician.dashboard');

});
Route::middleware(['auth', 'admin','prevent-back'])->group(function () {
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/{id}', [StaffController::class, 'show'])->name('staff.show');
});
// profile update routes for students
Route::middleware('auth','prevent-back')->group(function () {
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
});
//this is a profile for technician
Route::middleware('auth','prevent-back')->group(function () {
    Route::get('/adminEditProfile', [ProfileController::class, 'adminEditProfile'])->name('adminEdit');
    Route::post('/adminEditProfile', [ProfileController::class, 'adminUpdate'])->name('admin_profile.update');
});

// this for edit a profile for a technician
Route::middleware('auth','prevent-back','technician')->group(function () {

// Add the route for handling navigation API requests
    Route::post('/technician/route', [TechnicianController::class, 'getRoute'])->name('technician.route');
     Route::get('/technician/directions', [TechnicianController::class, 'directions'])->name('technician.directions');
    //notification routes for technician
     Route::get('/notifications/technician', [NotificationController::class, 'indexTechnician'])
        ->name('notification.index');
     Route::get('/Technician/notifications/{notification}', [NotificationController::class, 'showTechnician'])
        ->name('notifications.Techshow');

    Route::get('/technician/tasks/{task_id}', [TechnicianController::class, 'viewTaskDetails'])->name('technician.task_details');
    // Route to display the update form
    Route::get('/tasks/update/{task_id}', [TaskController::class, 'showUpdateForm'])->name('tasks.update.form');
    Route::put('/tasks/update/{task_id}', [TaskController::class, 'updateTask'])->name('tasks.update');
    Route::get('/technician/dashboard', [TechnicianController::class, 'dashboard'])->name('technician.dashboard');
    Route::get('/techEditProfile', [ProfileController::class, 'editProfile'])->name('tech_edit');
    Route::post('/techEditProfile', [ProfileController::class, 'techUpdate'])->name('tech_profile.update');
    Route::get('/techProfile', [ProfileController::class, 'techProfile'])->name('techProfile');
});
// Admin Feedback Routes
Route::middleware(['auth', 'admin','prevent-back'])->group(function() {
    // Admin routes
     Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/adminProfile', [ProfileController::class, 'adminProfile'])->name('adminProfile');
    Route::get('/feedbacks', [FeedbackController::class, 'index'])
        ->name('admin.feedbacks.index');

    Route::get('/feedbacks/export', [FeedbackController::class, 'export'])
        ->name('admin.feedbacks.export');

// location management routes
Route::resource('locations', LocationQrController::class)->except(['show']);
Route::get('locations', [LocationQRController::class, 'index'])->name('admin.locations.index');
Route::get('locations/{location}/edit', [LocationQrController::class, 'edit'])->name('admin.locations.edit');
Route::delete('locations/{location}', [LocationQrController::class, 'destroy'])->name('admin.locations.destroy');
Route::put('locations/{location}', [LocationQrController::class, 'update'])->name('admin.locations.update');



});


Route::get('/home', function () {
    return redirect()->route('Student.dashboard');
})->name('home');



Route::middleware('auth','prevent-back','campus_member')->group(function () {
   Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('Student.dashboard');
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
     Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/test-profile', [ProfileController::class, 'edit'])->name('test.profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');
Route::get('/notification/{notification}', [NotificationController::class, 'show'])
        ->name('notifications.show');
});

// Notification routes grouped under 'auth' middleware
Route::middleware('auth')->group(function () {

          Route::get('/notifications/admin', [NotificationController::class, 'indexAdmin'])
        ->name('notify.index');


    // Mark all as read (optional)
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.markAllRead');

    // Show specific notifications
    Route::get('/notification/{notification}', [NotificationController::class, 'show'])
        ->name('notifications.show');
    Route::get('/Technician/notifications/{notification}', [NotificationController::class, 'showTechnician'])
        ->name('notifications.Techshow');
    Route::get('/admin/notifications/{notification}', [NotificationController::class, 'showAdmin'])
        ->name('notifications.Adminshow');
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


    Route::get('/technician/directions', [TechnicianController::class, 'directions'])->name('technician.directions');

    // Add the route for handling navigation API requests
    Route::post('/technician/route', [TechnicianController::class, 'getRoute'])->name('technician.route');

    // Route to display the update form
    Route::get('/tasks/update/{task_id}', [TaskController::class, 'showUpdateForm'])->name('tasks.update.form');

    // Route to handle the update submission
    Route::put('/tasks/update/{task_id}', [TaskController::class, 'updateTask'])->name('tasks.update');
});


    // Location Management Routes


 Route::prefix('admin')->group(function() {
        Route::resource('students', \App\Http\Controllers\Admin\StudentController::class)
            ->names([
                'index' => 'admin.students.index',


            ]);
});

 Route::resource('technicians', TechnicianControllers::class)
            ->names([
                'index' => 'admin.technicians.index',
                'create' => 'admin.technicians.create',
                'store' => 'admin.technicians.store',
                'edit' => 'admin.technicians.edit',
                'update' => 'admin.technicians.update',
                'destroy' => 'admin.technicians.destroy'

        ]);


Route::get('admin/technicians/show/{id}', [TechnicianControllers::class, 'showTech'])->name('admin.technicians.show');
Route::post('/issues/{issue}/feedback', [FeedbackController::class, 'store'])
    ->name('feedback.submit')
    ->middleware('auth');


  Route::post('/logout', function () {
    Auth::logout();
    Session::invalidate();
    Session::regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Reports Routes

