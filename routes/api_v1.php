<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (V1)
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for version 1 of your application.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

// Public routes
Route::middleware(['api.rate.limit:30,1', 'api.logger'])->group(function () {
    Route::post('/login', 'Api\V1\AuthController@login');

    // Cacheable routes
    Route::middleware(['api.cache:300'])->group(function () {
        Route::get('/app-info', 'Api\V1\AuthController@appInfo');
    });
});

// Protected routes
Route::middleware(['auth:sanctum', 'api.rate.limit:60,1', 'api.logger'])->group(function () {
    // User routes
    Route::post('/logout', 'Api\V1\AuthController@logout');
    Route::get('/user', 'Api\V1\AuthController@user');
    Route::post('/change-password', 'Api\V1\AuthController@changePassword');

    // Admin routes
    Route::group(['prefix' => 'admin', 'middleware' => 'api.role:Admin', 'namespace' => 'Api\V1\Admin'], function () {
        // Students
        Route::apiResource('students', 'StudentController');
        Route::get('students/class/{classId}', 'StudentController@getStudentsByClass');
        Route::get('students/section/{sectionId}', 'StudentController@getStudentsBySection');

        // Teachers
        Route::apiResource('teachers', 'TeacherController');

        // Classes
        Route::apiResource('classes', 'ClassController');

        // Sections
        Route::apiResource('sections', 'SectionController');

        // Subjects
        Route::apiResource('subjects', 'SubjectController');

        // Exams
        Route::apiResource('exams', 'ExamController');

        // Dashboard
        Route::get('dashboard/stats', 'DashboardController@stats');
        Route::get('dashboard/enrollment', 'DashboardController@enrollment');
        Route::get('dashboard/gender-distribution', 'DashboardController@genderDistribution');
    });

    // Student routes
    Route::group(['prefix' => 'student', 'middleware' => 'api.role:Student', 'namespace' => 'Api\V1\Frontend'], function () {
        Route::get('/profile', 'StudentController@profile');
        Route::post('/profile', 'StudentController@updateProfile');
        Route::get('/attendance', 'StudentController@attendance');
        Route::get('/attendance/summary', 'StudentController@attendanceSummary');
        Route::get('/subjects', 'StudentController@subjects');
        Route::get('/results', 'StudentController@results');
        Route::get('/results/recent', 'StudentController@recentResults');
        Route::get('/fees', 'StudentController@fees');
        Route::get('/books', 'StudentController@books');
        Route::get('/timetable', 'StudentController@timetable');
    });

    // Teacher routes
    Route::group(['prefix' => 'teacher', 'middleware' => 'api.role:Teacher', 'namespace' => 'Api\V1\Frontend'], function () {
        Route::get('/profile', 'TeacherController@profile');
        Route::post('/profile', 'TeacherController@updateProfile');
        Route::get('/classes', 'TeacherController@classes');
        Route::get('/schedule/today', 'TeacherController@todaySchedule');
        Route::get('/attendance', 'TeacherController@attendance');
        Route::post('/attendance', 'TeacherController@storeAttendance');
        Route::get('/marks', 'TeacherController@marks');
        Route::post('/marks', 'TeacherController@storeMarks');
    });

    // Parent routes
    Route::group(['prefix' => 'parent', 'middleware' => 'api.role:Parent', 'namespace' => 'Api\V1\Frontend'], function () {
        Route::get('/profile', 'ParentController@profile');
        Route::post('/profile', 'ParentController@updateProfile');
        Route::get('/children', 'ParentController@children');
        Route::get('/children/{id}/attendance', 'ParentController@childAttendance');
        Route::get('/children/{id}/results', 'ParentController@childResults');
        Route::get('/children/{id}/fees', 'ParentController@childFees');
    });

    // Notifications
    Route::get('/notifications', 'Api\V1\NotificationController@index');
    Route::get('/notifications/unread', 'Api\V1\NotificationController@unread');
    Route::post('/notifications/{id}/read', 'Api\V1\NotificationController@markAsRead');
    Route::post('/notifications/read-all', 'Api\V1\NotificationController@markAllAsRead');
});
