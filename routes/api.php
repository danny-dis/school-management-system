<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::group(['namespace' => 'Api'], function () {
    // Auth routes
    Route::post('/login', 'AuthController@login');
    Route::get('/app-info', 'AuthController@appInfo');
});

// Protected routes
Route::group(['middleware' => ['auth:sanctum'], 'namespace' => 'Api'], function () {
    // Auth routes
    Route::post('/logout', 'AuthController@logout');
    Route::get('/user', 'AuthController@user');
    Route::post('/change-password', 'AuthController@changePassword');

    // Student routes
    Route::group(['prefix' => 'student', 'middleware' => 'api.role:Student'], function () {
        Route::get('/profile', 'StudentController@profile');
        Route::get('/attendance', 'StudentController@attendance');
        Route::get('/subjects', 'StudentController@subjects');
        Route::get('/results', 'StudentController@results');
        Route::get('/fees', 'StudentController@fees');
        Route::get('/books', 'StudentController@books');
    });

    // Teacher routes
    Route::group(['prefix' => 'teacher', 'middleware' => 'api.role:Teacher'], function () {
        Route::get('/profile', 'TeacherController@profile');
        Route::get('/subjects', 'TeacherController@subjects');
        Route::get('/classes', 'TeacherController@classes');
        Route::get('/students', 'TeacherController@students');
        Route::get('/attendance', 'TeacherController@attendance');
        Route::post('/attendance', 'TeacherController@saveAttendance');
        Route::get('/exams', 'TeacherController@exams');
        Route::get('/marks-form', 'TeacherController@marksForm');
        Route::post('/marks', 'TeacherController@saveMarks');
    });

    // Admin routes
    Route::group(['prefix' => 'admin', 'middleware' => 'api.role:Admin'], function () {
        // Admin routes will be added later
    });
});