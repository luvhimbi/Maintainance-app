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
use Illuminate\Http\Request;

Route::middleware(['auth', 'admin','prevent-back'])->group(function () {

    // Reports Routes (move these to the top of the admin group)
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


    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/profile', [ProfileController::class, 'adminProfile'])->name('adminProfile');
    Route::get('/admin/edit-profile', [ProfileController::class, 'adminEditProfile'])->name('adminEdit');
    Route::post('/admin/edit-profile', [ProfileController::class, 'adminUpdate'])->name('admin_profile.update');
    Route::get('admin/feedbacks', [FeedbackController::class, 'index'])->name('admin.feedbacks.index');
    Route::get('/feedbacks/export', [FeedbackController::class, 'export'])->name('admin.feedbacks.export');
    Route::get('/admin/notifications', [NotificationController::class, 'indexAdmin'])->name('notify.index');
    Route::get('/admin/notifications/{notification}', [NotificationController::class, 'showAdmin'])->name('notifications.Adminshow');
    Route::get('admin/staff_member', [StaffController::class, 'index'])->name('staff.index');
    Route::get('admin/staff/{id}', [StaffController::class, 'show'])->name('staff.show');

    // Index - List all technicians
Route::get('admin/technicians', [TechnicianControllers::class, 'index'])
->name('admin.technicians.index');

// Create - Show form to create a technician
Route::get('admin/technicians/create', [TechnicianControllers::class, 'create'])
->name('admin.technicians.create');

// Store - Save new technician
Route::post('admin/technicians', [TechnicianControllers::class, 'store'])
->name('admin.technicians.store');

// Edit - Show form to edit technician
Route::get('admin/technicians/edit/{technician}', [TechnicianControllers::class, 'edit'])
->name('admin.technicians.edit');

// Update - Update technician
Route::put('admin/technicians/{technician}', [TechnicianControllers::class, 'update'])
->name('admin.technicians.update');

Route::get('admin/students', [\App\Http\Controllers\Admin\StudentController::class, 'index'])
->name('admin.students.index');
 Route::get('admin/technicians/show/{id}', [TechnicianControllers::class, 'showTech'])->name('admin.technicians.show');
// Destroy - Delete technician
Route::delete('admin/technicians/{technician}', [TechnicianControllers::class, 'destroy'])
->name('admin.technicians.destroy');
    //locations routes for admin to manage campus locations
    Route::get('admin/campus-locations/create', [LocationQRController::class, 'create'])->name('admin.locations.create');
    Route::post('/locations', [LocationQrController::class, 'store'])->name('admin.locations.store');
    Route::get('admin/campus-locations', [LocationQRController::class, 'index'])->name('admin.locations.index');
    Route::get('admin/campus-locations/edit/{location}', [LocationQrController::class, 'edit'])->name('admin.locations.edit');
    Route::delete('locations/{location}', [LocationQrController::class, 'destroy'])->name('admin.locations.destroy');
    Route::put('locations/{location}', [LocationQrController::class, 'update'])->name('admin.locations.update');
    // Admin route to view tasks
    Route::get('/admin/tasks/view', [TaskController::class, 'viewTasks'])->name('admin.tasks.view');
    // Route to view task progress
    Route::get('admin/tasks/progress/{task}', [TaskAssignmentController::class, 'show'])->name('tasks.progress.show');
    Route::post('/admin/tasks/{task}/send-reminder', [TaskController::class, 'sendReminder'])->name('admin.tasks.sendReminder');
    Route::post('/admin/tasks/{task}/reassign', [TaskController::class, 'reassignTask'])->name('admin.tasks.reassign');
});




Route::get('/', [HomeController::class, 'index'])->name('home');

// Show login form and handle submissions
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Password reset route
Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset.form');
Route::post('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/leaving', [App\Http\Controllers\LeaveSiteController::class, 'show'])->name('leaving');


// this for edit a profile for a technician
Route::middleware('auth','prevent-back','technician')->group(function () {

    Route::get('/technician/dashboard', [TechnicianController::class, 'dashboard'])->name('technician.dashboard');

    Route::get('/technician/SmartNav', [TechnicianController::class, 'directions'])->name('technician.directions');

    Route::post('/technician/route', [TechnicianController::class, 'getRoute'])->name('technician.route');

    Route::get('technician/completed-tasks', [TaskController::class, 'completedTasks'])->name('completed.tasks');

    Route::get('/technician/tasks/{task_id}', [TechnicianController::class, 'viewTaskDetails'])->name('technician.task_details');

    // Route to display the update form
    Route::get('technician/tasks/update/{task_id}', [TaskController::class, 'showUpdateForm'])->name('tasks.update.form');

    Route::put('technician/task-update/{task_id}', [TaskController::class, 'updateTask'])->name('tasks.update');

    Route::get('technician/completed-task/{task_id}', [TaskController::class, 'taskUpdates'])->name('tasks.updates');

    //notification routes for technician
     Route::get('/notifications/technician', [NotificationController::class, 'indexTechnician'])
        ->name('notification.index');
     Route::get('/Technician/notifications/{notification}', [NotificationController::class, 'showTechnician'])
        ->name('notifications.Techshow');


   //profile routes for technician
    Route::get('technician/profile', [ProfileController::class, 'techProfile'])->name('techProfile');
    Route::get('technician/edit-profile', [ProfileController::class, 'editProfile'])->name('tech_edit');
    Route::post('/techEditProfile', [ProfileController::class, 'techUpdate'])->name('tech_profile.update');

});




Route::middleware('auth','prevent-back','campus_member')->group(function () {
    // this a route to get the student/staff dashboard
    Route::get('/user/dashboard', [StudentController::class, 'dashboard'])->name('Student.dashboard');
   // this is a route for student/staff to fill out a form to report an issue
    Route::get('/user/report-issue', [IssueController::class, 'create'])->name('Student.createissue');

    Route::get('user/issues/{issue}/edit', [IssueController::class, 'editReportedIssue'])->name('Student.editissue');

    Route::post('/report-issue', [IssueController::class, 'store'])->name('issue.store');

    Route::post('/save-issue', [IssueController::class, 'save'])->name('issue.save');

    Route::get('user/view-issues', [IssueController::class, 'viewAllIssues'])->name('Student.view_issues');

    Route::get('user/view-issues/{id}', [IssueController::class, 'viewIssueDetails'])->name('Student.issue_details');

    Route::put('/issues/{issue}', [IssueController::class, 'update'])->name('Student.updateissue');

    Route::get('/user/report-issue/confirm', [IssueController::class, 'confirm'])->name('Student.confirmissue');

    Route::get('/student/issues/{issue}/edit', [IssueController::class, 'edit'])->name('Student.edit_issue');

    Route::put('/student/issues/{issue}', [IssueController::class, 'update'])->name('Student.update_issue');

    Route::get('/user/report-issue/success', [IssueController::class, 'success'])->name('issue.success');

    Route::get('/edit-issue', [IssueController::class, 'edit'])->name('issue.edit');

    Route::get('user/profile', [ProfileController::class, 'index'])->name('profile');

    Route::get('user/profile/edit-profile', [ProfileController::class, 'edit'])->name('test.profile.edit');

    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    Route::get('user/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::get('notification/{notification}', [NotificationController::class, 'show'])
        ->name('notifications.show');

        Route::post('/issues/{issue}/feedback', [FeedbackController::class, 'store'])
    ->name('feedback.submit');
});

// shared routes for both students and staff members ,admin and technician
Route::middleware('auth')->group(function () {


    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    // Mark all as read (optional)
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.markAllRead');

    // Delete notifications
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');

     Route::delete('/notifications/bulk-delete', [NotificationController::class, 'bulkDestroy'])
        ->name('notifications.bulkDestroy');
});



//  Route::resource('admin/technicians', TechnicianControllers::class)
//             ->names([
//                 'index' => 'admin.technicians.index',
//                 'create' => 'admin.technicians.create',
//                 'store' => 'admin.technicians.store',
//                 'edit' => 'admin.technicians.edit',
//                 'update' => 'admin.technicians.update',
//                 'destroy' => 'admin.technicians.destroy'

//         ]);






Route::post('/logout', function () {
    Auth::logout();
    Session::invalidate();
    Session::regenerateToken();
    return redirect()->route('login')->with('status', 'You have been logged out successfully.');
})->name('logout');

// Reports Routes

Route::middleware(['auth'])->prefix('technician')->name('technician.')->group(function () {
    // Use Scout for case-insensitive search (convert query to lowercase)
    Route::get('/search-locations', function (Request $request) {
        $query = strtolower($request->get('query', ''));
        return \App\Models\Location::search($query)
            ->take(10)
            ->get(['location_id', 'building_name', 'floor_number', 'room_number', 'description', 'latitude', 'longitude']);
    });
});

