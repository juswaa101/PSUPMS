<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\TaskColorController;
use App\Http\Controllers\BoardColorController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login/checkAuth', [LoginController::class, 'authLogin']);
Route::get('/logout', [LoginController::class, 'logout']);

// Admin role
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'isAdmin']], function () {
    Route::get('/dashboard', [LoginController::class, 'adminDashboard']);

    // User management routes
    Route::group(['prefix' => 'user-management'], function () {
        Route::get('/', [UserManagementController::class, 'index']);
        Route::get('/fetch', [UserManagementController::class, 'fetchUsers']);
        Route::post('/create', [UserManagementController::class, 'create'])->name('user.create');
        Route::delete('/delete/{id}', [UserManagementController::class, 'destroy']);
        Route::get('/edit/{id}', [UserManagementController::class, 'edit']);
        Route::post('/update/{id}', [UserManagementController::class, 'update']);
    });

    // Project routes
    Route::group(['prefix' => 'project'], function () {
        Route::get('/', [ProjectController::class, 'index']);
        Route::get('/findStaff', [ProjectController::class, 'getStaff']);
        Route::get('/findHead', [ProjectController::class, 'getHead']);
        Route::post('/store', [ProjectController::class, 'store'])->name('project.store');
        Route::delete('/delete/{title}', [KanbanController::class, 'destroy']);
        Route::put('/update/{id}', [ProjectController::class, 'update']);
        Route::get('/{id}', [KanbanController::class, 'index']);
    });

    // Member assigning routes
    Route::put('/update-members/{id}', [KanbanController::class, 'updateMembers']);
    Route::put('/task/update-members/{id}', [TaskController::class, 'assignTaskMember']);
    Route::get('/task/assigned/{id}', [TaskController::class, 'assignedTaskMember']);

    // Subtask routes
    Route::group(['prefix' => 'subtask'], function () {
        Route::get('/{task_id}/{user_id}', [SubtaskController::class, 'index']);
        Route::get('/{id}/{task_id}/{user_id}', [SubtaskController::class, 'show']);
        Route::post('/create', [SubtaskController::class, 'store']);
        Route::delete('/delete/{id}', [SubtaskController::class, 'destroy']);
        Route::put('/update/{id}/{task_id}/{user_id}', [SubtaskController::class, 'update']);
    });

    // Comment routes
    Route::group(['prefix' => 'comment'], function () {
        Route::get('/{id}', [CommentController::class, 'show']);
        Route::post('/store', [CommentController::class, 'store']);
        Route::delete('/delete/{id}', [CommentController::class, 'destroy']);
        Route::put('/update/{id}', [CommentController::class, 'update']);
    });

    // File routes
    Route::group(['prefix' => 'file'], function () {
        Route::post('/upload', [UploadController::class, 'store']);
        Route::get('/download/{file}', [UploadController::class, 'download']);
        Route::delete('/destroy/{id}/{file}', [UploadController::class, 'destroy']);
        Route::get('/uploads/{task_id}', [UploadController::class, 'show']);
    });

    // Report routes
    Route::group(['prefix' => 'reports'], function () {
        Route::get('/', [ReportsController::class, 'adminReports']);
        Route::get('/fetch', [ReportsController::class, 'fetchProject']);
        Route::get('/fetch/{id}', [ReportsController::class, 'showReport']);
        Route::get('/generate-report/{id}', [ReportsController::class, 'exportPdfAdmin']);
        Route::get('/fetch/project/{id}', [ReportsController::class, 'showEmployees']);
    });

    Route::put('/update-order', [BoardController::class, 'updateOrder']);
    Route::get('/check-conflict', [ProjectController::class, 'getAllDueDate']);
    Route::get('/total-subtask/{id}', [TaskController::class, 'taskProgress']);
    Route::get('/finish-project/{id}', [ProjectController::class, 'toggleFinishedProject']);
    Route::get('/board-color/{id}', [BoardColorController::class, 'getBoardColor']);
    Route::put('/board-color/update/{id}', [BoardColorController::class, 'updateBoardColor']);
    Route::get('/task-color/{id}', [TaskColorController::class, 'getTaskColor']);
    Route::put('/task-color/update/{id}', [TaskColorController::class, 'updateTaskColor']);
});

// Head and member role
Route::group(['prefix' => 'head', 'middleware' => ['auth', 'isUser']], function () {
    Route::get('/dashboard/{id}', [LoginController::class, 'headDashboard']);
    Route::group(['prefix' => 'project'], function () {
        Route::get('/store', [ProjectController::class, 'head']);
        Route::post('/create', [ProjectController::class, 'storeHeadProject'])->name('head.store');
        Route::get('/{id}', [KanbanController::class, 'projectHead']);
        Route::delete('/delete/{title}', [KanbanController::class, 'destroy']);
        Route::put('/update/{id}', [ProjectController::class, 'update']);
        Route::get('/findStaff', [ProjectController::class, 'getStaff']);
        Route::get('/findHead', [ProjectController::class, 'getHead']);
        Route::get('/check-conflict', [ProjectController::class, 'getAllDueDate']);
    });

    // Member assigning routes
    Route::put('/update-members/{id}', [KanbanController::class, 'updateMembers']);
    Route::put('/task/update-members/{id}', [TaskController::class, 'assignTaskMember']);
    Route::get('/task/assigned/{id}', [TaskController::class, 'assignedTaskMember']);

    // Subtask routes
    Route::group(['prefix' => 'subtask'], function () {
        Route::get('/{task_id}/{user_id}', [SubtaskController::class, 'index']);
        Route::get('/{id}/{task_id}/{user_id}', [SubtaskController::class, 'show']);
        Route::post('/create', [SubtaskController::class, 'store']);
        Route::delete('/delete/{id}', [SubtaskController::class, 'destroy']);
        Route::put('/update/{id}/{task_id}/{user_id}', [SubtaskController::class, 'update']);
    });

    // Comment routes
    Route::group(['prefix' => 'comment'], function () {
        Route::get('/{id}', [CommentController::class, 'show']);
        Route::post('/store', [CommentController::class, 'store']);
        Route::delete('/delete/{id}', [CommentController::class, 'destroy']);
        Route::put('/update/{id}', [CommentController::class, 'update']);
    });

    // File routes
    Route::group(['prefix' => 'file'], function () {
        Route::post('/upload', [UploadController::class, 'store']);
        Route::get('/download/{file}', [UploadController::class, 'download']);
        Route::delete('/destroy/{id}/{file}', [UploadController::class, 'destroy']);
        Route::get('/uploads/{task_id}', [UploadController::class, 'show']);
    });

    Route::group(['prefix' => 'reports'], function () {
        Route::get('/', [ReportsController::class, 'headReports']);
        Route::get('/fetch', [ReportsController::class, 'fetchHeadProject']);
        Route::get('/fetch/{id}', [ReportsController::class, 'showReportHead']);
        Route::get('/generate-report/{id}', [ReportsController::class, 'exportPdfHead']);
        Route::get('/fetch/project/{id}', [ReportsController::class, 'showEmployees']);
    });

    Route::put('/update-order', [BoardController::class, 'updateOrder']);
    Route::get('/check-conflict', [ProjectController::class, 'getAllDueDate']);
    Route::get('/total-subtask/{id}', [TaskController::class, 'taskProgress']);
    Route::get('/finish-project/{id}', [ProjectController::class, 'toggleFinishedProject']);
    Route::get('/board-color/{id}', [BoardColorController::class, 'getBoardColor']);
    Route::put('/board-color/update/{id}', [BoardColorController::class, 'updateBoardColor']);
    Route::get('/task-color/{id}', [TaskColorController::class, 'getTaskColor']);
    Route::put('/task-color/update/{id}', [TaskColorController::class, 'updateTaskColor']);
});


// Notifications
Route::group(['middleware' => 'auth'], function () {
    Route::get('/delete/notification/{id}', [NotificationController::class, 'dismissNotification']);
    Route::get('/read/notification/{id}', [NotificationController::class, 'markAsReadNotification']);
    Route::get('/unread/notification/{id}', [NotificationController::class, 'markAsUnreadNotification']);
    Route::get('/count/notification', [NotificationController::class, 'countUnreadNotification']);
    Route::get('/read-all/notification', [NotificationController::class, 'markAllNotificationAsRead']);
});

// Invitations
Route::group(['middleware' => 'auth'], function () {
    Route::get('/count/invitations', [InvitationController::class, 'countInvitation']);
    Route::get('/accept-invitation/{id}', [InvitationController::class, 'acceptInvitation']);
    Route::get('/reject-invitation/{id}', [InvitationController::class, 'rejectInvitation']);
});
