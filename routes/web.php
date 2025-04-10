<?php

use App\Http\Helpers\AppHelper;
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

/**
 * Admin panel routes goes below
 */
Route::group(
    ['namespace' => 'Backend', 'middleware' => ['guest']], function () {
    Route::get('/login', 'UserController@login')->name('login');
    Route::post('/login', 'UserController@authenticate');
    Route::get('/forgot', 'UserController@forgot')->name('forgot');
    Route::post('/forgot', 'UserController@forgot')
        ->name('forgot');
    Route::get('/reset/{token}', 'UserController@reset')
        ->name('reset');
    Route::post('/reset/{token}', 'UserController@reset')
        ->name('reset');

}
);

Route::get('/public/exam', 'Backend\ExamController@indexPublic')->name('public.exam_list');
Route::any('/online-result', 'Backend\ReportController@marksheetPublic')->name('report.marksheet_pub');

Route::group(
    ['namespace' => 'Backend', 'middleware' => ['auth', 'permission']], function () {
    Route::get('/logout', 'UserController@logout')->name('logout');
    Route::get('/lock', 'UserController@lock')->name('lockscreen');
    Route::get('/dashboard', 'UserController@dashboard')->name('user.dashboard');

    //user management
    Route::resource('user', 'UserController');
    Route::get('/profile', 'UserController@profile')
        ->name('profile');
    Route::post('/profile', 'UserController@profile')
        ->name('profile');
    Route::get('/change-password', 'UserController@changePassword')
        ->name('change_password');
    Route::post('/change-password', 'UserController@changePassword')
        ->name('change_password');
    Route::post('user/status/{id}', 'UserController@changeStatus')
        ->name('user.status');
    Route::any('user/{id}/permission', 'UserController@updatePermission')
        ->name('user.permission');

    //user notification
    Route::get('/notification/unread', 'NotificationController@getUnReadNotification')
        ->name('user.notification_unread');
    Route::get('/notification/read', 'NotificationController@getReadNotification')
        ->name('user.notification_read');
    Route::get('/notification/all', 'NotificationController@getAllNotification')
        ->name('user.notification_all');

    //system user management
    Route::get('/administrator/user', 'AdministratorController@userIndex')
        ->name('administrator.user_index');
    Route::get('/administrator/user/create', 'AdministratorController@userCreate')
        ->name('administrator.user_create');
    Route::post('/administrator/user/store', 'AdministratorController@userStore')
        ->name('administrator.user_store');
    Route::get('/administrator/user/{id}/edit', 'AdministratorController@userEdit')
        ->name('administrator.user_edit');
    Route::post('/administrator/user/{id}/update', 'AdministratorController@userUpdate')
        ->name('administrator.user_update');
    Route::post('/administrator/user/{id}/delete', 'AdministratorController@userDestroy')
        ->name('administrator.user_destroy');
    Route::post('administrator/user/status/{id}', 'AdministratorController@userChangeStatus')
        ->name('administrator.user_status');

    Route::any('/administrator/user/reset-password', 'AdministratorController@userResetPassword')
        ->name('administrator.user_password_reset');



    //user role manage
    Route::get('/role', 'UserController@roles')
        ->name('user.role_index');
    Route::post('/role', 'UserController@roles')
        ->name('user.role_destroy');
    Route::get('/role/create', 'UserController@roleCreate')
        ->name('user.role_create');
    Route::post('/role/store', 'UserController@roleCreate')
        ->name('user.role_store');
    Route::any('/role/update/{id}', 'UserController@roleUpdate')
        ->name('user.role_update');


    // application settings routes
    Route::get('settings/institute', 'SettingsController@institute')
        ->name('settings.institute');
    Route::post('settings/institute', 'SettingsController@institute')
        ->name('settings.institute');

    //report settings
    Route::get('settings/report', 'SettingsController@report')
        ->name('settings.report');
    Route::post('settings/report', 'SettingsController@report')
        ->name('settings.report');


    // Module management routes
    Route::get('modules', 'ModuleController@index')->name('modules.index');
    Route::post('modules/enable', 'ModuleController@enable')->name('modules.enable');
    Route::post('modules/disable', 'ModuleController@disable')->name('modules.disable');

    // administrator routes
    //academic year
    Route::get('administrator/academic_year', 'AdministratorController@academicYearIndex')
        ->name('administrator.academic_year');
    Route::post('administrator/academic_year', 'AdministratorController@academicYearIndex')
        ->name('administrator.academic_year_destroy');
    Route::get('administrator/academic_year/create', 'AdministratorController@academicYearCru')
        ->name('administrator.academic_year_create');
    Route::post('administrator/academic_year/create', 'AdministratorController@academicYearCru')
        ->name('administrator.academic_year_store');
    Route::get('administrator/academic_year/edit/{id}', 'AdministratorController@academicYearCru')
        ->name('administrator.academic_year_edit');
    Route::post('administrator/academic_year/update/{id}', 'AdministratorController@academicYearCru')
        ->name('administrator.academic_year_update');
    Route::post('administrator/academic_year/status/{id}', 'AdministratorController@academicYearChangeStatus')
        ->name('administrator.academic_year_status');


    // academic routes
    // class
    Route::get('academic/class', 'AcademicController@classIndex')
        ->name('academic.class');
    Route::post('academic/class', 'AcademicController@classIndex')
        ->name('academic.class_destroy');
    Route::get('academic/class/create', 'AcademicController@classCru')
        ->name('academic.class_create');
    Route::post('academic/class/create', 'AcademicController@classCru')
        ->name('academic.class_store');
    Route::get('academic/class/edit/{id}', 'AcademicController@classCru')
        ->name('academic.class_edit');
    Route::post('academic/class/update/{id}', 'AcademicController@classCru')
        ->name('academic.class_update');
    Route::post('academic/class/status/{id}', 'AcademicController@classStatus')
        ->name('academic.class_status');

    // section
    Route::get('academic/section', 'AcademicController@sectionIndex')
        ->name('academic.section');
    Route::post('academic/section', 'AcademicController@sectionIndex')
        ->name('academic.section_destroy');
    Route::get('academic/section/create', 'AcademicController@sectionCru')
        ->name('academic.section_create');
    Route::post('academic/section/create', 'AcademicController@sectionCru')
        ->name('academic.section_store');
    Route::get('academic/section/edit/{id}', 'AcademicController@sectionCru')
        ->name('academic.section_edit');
    Route::post('academic/section/update/{id}', 'AcademicController@sectionCru')
        ->name('academic.section_update');
    Route::post('academic/section/status/{id}', 'AcademicController@sectionStatus')
        ->name('academic.section_status');

    // subject
    Route::get('academic/subject', 'AcademicController@subjectIndex')
        ->name('academic.subject');
    Route::post('academic/subject', 'AcademicController@subjectIndex')
        ->name('academic.subject_destroy');
    Route::get('academic/subject/create', 'AcademicController@subjectCru')
        ->name('academic.subject_create');
    Route::post('academic/subject/create', 'AcademicController@subjectCru')
        ->name('academic.subject_store');
    Route::get('academic/subject/edit/{id}', 'AcademicController@subjectCru')
        ->name('academic.subject_edit');
    Route::post('academic/subject/update/{id}', 'AcademicController@subjectCru')
        ->name('academic.subject_update');
    Route::post('academic/subject/status/{id}', 'AcademicController@subjectStatus')
        ->name('academic.subject_status');


    // teacher routes
    Route::resource('teacher', 'TeacherController');
    Route::post('teacher/status/{id}', 'TeacherController@changeStatus')
        ->name('teacher.status');

    // student routes
    Route::resource('student', 'StudentController');
    Route::post('student/status/{id}', 'StudentController@changeStatus')
        ->name('student.status');
    Route::get('student-list-by-filter', 'StudentController@studentListByFitler')
        ->name('student.list_by_filter');

    // student attendance routes
    Route::get('student-attendance', 'StudentAttendanceController@index')->name('student_attendance.index');
    Route::any('student-attendance/create', 'StudentAttendanceController@create')->name('student_attendance.create');
    Route::post('student-attendance/store', 'StudentAttendanceController@store')->name('student_attendance.store');
    Route::post('student-attendance/status/{id}', 'StudentAttendanceController@changeStatus')
        ->name('student_attendance.status');

    // HRM
    //Employee
    Route::resource('hrm/employee', 'EmployeeController', ['as' => 'hrm']);
    Route::post('hrm/employee/status/{id}', 'EmployeeController@changeStatus')
        ->name('hrm.employee.status');
    // Leave
    Route::resource('hrm/leave', 'LeaveController', ['as' => 'hrm']);
    // policy
    Route::get('hrm/policy', 'EmployeeController@hrmPolicy')
        ->name('hrm.policy');
    Route::post('hrm/policy', 'EmployeeController@hrmPolicy')
        ->name('hrm.policy');

    // employee attendance routes
    Route::get('employee-attendance', 'EmployeeAttendanceController@index')->name('employee_attendance.index');
    Route::get('employee-attendance/create', 'EmployeeAttendanceController@create')->name('employee_attendance.create');
    Route::post('employee-attendance/create', 'EmployeeAttendanceController@store')->name('employee_attendance.store');
    Route::post('employee-attendance/status/{id}', 'EmployeeAttendanceController@changeStatus')
        ->name('employee_attendance.status');


    //exam
    Route::get('exam', 'ExamController@index')
        ->name('exam.index');
    Route::get('exam/create', 'ExamController@create')
        ->name('exam.create');
    Route::post('exam/store', 'ExamController@store')
        ->name('exam.store');
    Route::get('exam/edit/{id}', 'ExamController@edit')
        ->name('exam.edit');
    Route::post('exam/update/{id}', 'ExamController@update')
        ->name('exam.update');
    Route::post('exam/status/{id}', 'ExamController@changeStatus')
        ->name('exam.status');
    Route::post('exam/delete/{id}', 'ExamController@destroy')
        ->name('exam.destroy');
    //grade
    Route::get('exam/grade', 'ExamController@gradeIndex')
        ->name('exam.grade.index');
    Route::post('exam/grade', 'ExamController@gradeIndex')
        ->name('exam.grade.destroy');
    Route::get('exam/grade/create', 'ExamController@gradeCru')
        ->name('exam.grade.create');
    Route::post('exam/grade/create', 'ExamController@gradeCru')
        ->name('exam.grade.store');
    Route::get('exam/grade/edit/{id}', 'ExamController@gradeCru')
        ->name('exam.grade.edit');
    Route::post('exam/grade/update/{id}', 'ExamController@gradeCru')
        ->name('exam.grade.update');
    //exam rules
    Route::get('exam/rule', 'ExamController@ruleIndex')
        ->name('exam.rule.index');
    Route::post('exam/rule', 'ExamController@ruleIndex')
        ->name('exam.rule.destroy');
    Route::get('exam/rule/create', 'ExamController@ruleCreate')
        ->name('exam.rule.create');
    Route::post('exam/rule/create', 'ExamController@ruleCreate')
        ->name('exam.rule.store');
    Route::get('exam/rule/edit/{id}', 'ExamController@ruleEdit')
        ->name('exam.rule.edit');
    Route::post('exam/rule/update/{id}', 'ExamController@ruleEdit')
        ->name('exam.rule.update');

    //Marks
    Route::any('marks', 'MarkController@index')
        ->name('marks.index');
    Route::any('marks/create', 'MarkController@create')
        ->name('marks.create');
    Route::post('marks/store', 'MarkController@store')
        ->name('marks.store');
    Route::get('marks/edit/{id}', 'MarkController@edit')
        ->name('marks.edit');
    Route::post('marks/update/{id}', 'MarkController@update')
        ->name('marks.update');
    //result
    Route::any('result', 'MarkController@resultIndex')
        ->name('result.index');
    Route::any('result/generate', 'MarkController@resultGenerate')
        ->name('result.create');
    Route::any('result/delete', 'MarkController@resultDelete')
        ->name('result.delete');

    // Promotion
    Route::any('promotion', 'MarkController@promotion')
        ->name('promotion.create');
    Route::post('do-promotion', 'MarkController@doPromotion')
        ->name('promotion.store');


    // Reporting
    Route::any('report/student-monthly-attendance', 'ReportController@studentMonthlyAttendance')
        ->name('report.student_monthly_attendance');
    Route::any('report/student-list', 'ReportController@studentList')
        ->name('report.student_list');
    Route::any('report/employee-list', 'ReportController@employeeList')
        ->name('report.employee_list');
    Route::any('report/employee-monthly-attendance', 'ReportController@employeeMonthlyAttendance')
        ->name('report.employee_monthly_attendance');

});

//non privilege routes
Route::group(['namespace' => 'Backend', 'middleware' => ['auth']], function () {
    Route::get('/st-profile', 'PublicController@studentProfile')
        ->name('public.student_profile');
    Route::get('public/get-student-attendance', 'PublicController@getStudentAttendance')
        ->name('public.get_student_attendance');
    Route::get('public/check-section-empty-seat', 'PublicController@checkSectionHaveEmptySeat')
        ->name('public.section_capacity_check');
    Route::get('public/promotional-year-list', 'PublicController@getAcademicYearsForPromotion')
        ->name('public.get_promotional_year_list');
    Route::get('public/class-subject-count', 'PublicController@getClassSubjectCountNewAlgo')
        ->name('public.class_subject_count');

    Route::get('/public/get-student-result', 'PublicController@getStudentResults')
        ->name('public.get_student_result');
    Route::get('/public/get-student-subject', 'PublicController@getStudentSubject')
        ->name('public.get_student_subject');
    Route::get('/public/get-subject-settings/{classId}', 'PublicController@getClassSubjectSettings')
        ->name('public.get_class_subject_settings');
    Route::get('/public/get-section', 'AcademicController@sectionIndex')
        ->name('public.section');


    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')
        ->name('app_log');
});

// Student Portal Routes
Route::group(['namespace' => 'Backend', 'middleware' => ['auth', 'student', 'module:student_portal'], 'prefix' => 'student-portal'], function () {
    // Dashboard
    Route::get('/dashboard', 'StudentPortalController@dashboard')
        ->name('student.portal.dashboard');

    // Attendance
    Route::get('/attendance', 'StudentPortalController@attendance')
        ->name('student.portal.attendance');

    // Grades
    Route::get('/grades', 'StudentPortalController@grades')
        ->name('student.portal.grades');

    // Subjects
    Route::get('/subjects', 'StudentPortalController@subjects')
        ->name('student.portal.subjects');

    // Profile
    Route::get('/profile', 'StudentPortalController@profile')
        ->name('student.portal.profile');
    Route::post('/profile/update', 'StudentPortalController@updateProfile')
        ->name('student.portal.update_profile');

    // Change Password
    Route::get('/change-password', 'StudentPortalController@changePassword')
        ->name('student.portal.change_password');
    Route::post('/change-password', 'StudentPortalController@changePassword')
        ->name('student.portal.change_password');
});

// Health and Medical Records Routes
Route::group(['namespace' => 'Backend', 'middleware' => ['auth', 'module:health'], 'prefix' => 'health'], function () {
    // Health Records
    Route::get('/', 'HealthController@index')->name('health.index');
    Route::get('/create', 'HealthController@create')->name('health.create');
    Route::post('/', 'HealthController@store')->name('health.store');
    Route::get('/{id}', 'HealthController@show')->name('health.show');
    Route::get('/{id}/edit', 'HealthController@edit')->name('health.edit');
    Route::put('/{id}', 'HealthController@update')->name('health.update');

    // Medical Visits
    Route::get('/{id}/visits/create', 'HealthController@createVisit')->name('health.visits.create');
    Route::post('/{id}/visits', 'HealthController@storeVisit')->name('health.visits.store');
    Route::get('/{id}/visits/{visitId}/edit', 'HealthController@editVisit')->name('health.visits.edit');
    Route::put('/{id}/visits/{visitId}', 'HealthController@updateVisit')->name('health.visits.update');

    // Vaccinations
    Route::get('/vaccinations/index', 'HealthController@vaccinations')->name('health.vaccinations');
    Route::get('/vaccinations/create', 'HealthController@createVaccination')->name('health.vaccinations.create');
    Route::post('/vaccinations', 'HealthController@storeVaccination')->name('health.vaccinations.store');
    Route::get('/vaccinations/{id}/edit', 'HealthController@editVaccination')->name('health.vaccinations.edit');
    Route::put('/vaccinations/{id}', 'HealthController@updateVaccination')->name('health.vaccinations.update');

    // Vaccination Records
    Route::get('/{id}/vaccinations/record', 'HealthController@recordVaccination')->name('health.vaccinations.record');
    Route::post('/{id}/vaccinations', 'HealthController@storeVaccinationRecord')->name('health.vaccinations.record.store');

    // Reports
    Route::get('/reports/index', 'HealthController@reports')->name('health.reports');
    Route::post('/reports/generate', 'HealthController@generateReport')->name('health.reports.generate');
});

// Transportation Management Routes
Route::group(['namespace' => 'Backend', 'middleware' => ['auth', 'module:transportation'], 'prefix' => 'transportation'], function () {
    // Vehicles
    Route::get('/vehicles', 'TransportationController@vehicles')->name('transportation.vehicles');
    Route::get('/vehicles/create', 'TransportationController@createVehicle')->name('transportation.vehicles.create');
    Route::post('/vehicles', 'TransportationController@storeVehicle')->name('transportation.vehicles.store');
    Route::get('/vehicles/{id}', 'TransportationController@showVehicle')->name('transportation.vehicles.show');
    Route::get('/vehicles/{id}/edit', 'TransportationController@editVehicle')->name('transportation.vehicles.edit');
    Route::put('/vehicles/{id}', 'TransportationController@updateVehicle')->name('transportation.vehicles.update');
    Route::delete('/vehicles/{id}', 'TransportationController@destroyVehicle')->name('transportation.vehicles.destroy');

    // Routes
    Route::get('/routes', 'TransportationController@routes')->name('transportation.routes');
    Route::get('/routes/create', 'TransportationController@createRoute')->name('transportation.routes.create');
    Route::post('/routes', 'TransportationController@storeRoute')->name('transportation.routes.store');
    Route::get('/routes/{id}', 'TransportationController@showRoute')->name('transportation.routes.show');
    Route::get('/routes/{id}/edit', 'TransportationController@editRoute')->name('transportation.routes.edit');
    Route::put('/routes/{id}', 'TransportationController@updateRoute')->name('transportation.routes.update');
    Route::delete('/routes/{id}', 'TransportationController@destroyRoute')->name('transportation.routes.destroy');

    // Stops
    Route::get('/routes/{id}/stops', 'TransportationController@routeStops')->name('transportation.routes.stops');
    Route::post('/routes/{id}/stops', 'TransportationController@storeStop')->name('transportation.routes.stops.store');
    Route::get('/routes/{id}/stops/{stopId}/edit', 'TransportationController@editStop')->name('transportation.routes.stops.edit');
    Route::put('/routes/{id}/stops/{stopId}', 'TransportationController@updateStop')->name('transportation.routes.stops.update');
    Route::delete('/routes/{id}/stops/{stopId}', 'TransportationController@destroyStop')->name('transportation.routes.stops.destroy');

    // Students
    Route::get('/students', 'TransportationController@students')->name('transportation.students');
    Route::get('/students/assign', 'TransportationController@assignStudent')->name('transportation.students.assign');
    Route::post('/students', 'TransportationController@storeStudentAssignment')->name('transportation.students.store');
    Route::get('/students/{id}/edit', 'TransportationController@editStudentAssignment')->name('transportation.students.edit');
    Route::put('/students/{id}', 'TransportationController@updateStudentAssignment')->name('transportation.students.update');
    Route::delete('/students/{id}', 'TransportationController@destroyStudentAssignment')->name('transportation.students.destroy');

    // AJAX routes
    Route::post('/get-route-stops', 'TransportationController@getRouteStops')->name('transportation.get_route_stops');

    // Reports
    Route::get('/reports', 'TransportationController@reports')->name('transportation.reports');
    Route::post('/reports/generate', 'TransportationController@generateReport')->name('transportation.reports.generate');
});

// Timetable and Scheduling Routes
Route::group(['namespace' => 'Backend', 'middleware' => ['auth', 'module:timetable'], 'prefix' => 'timetable'], function () {
    // Timetables
    Route::get('/', 'TimetableController@index')->name('timetable.index');
    Route::get('/create', 'TimetableController@create')->name('timetable.create');
    Route::post('/', 'TimetableController@store')->name('timetable.store');
    Route::get('/{id}', 'TimetableController@show')->name('timetable.show');
    Route::get('/{id}/edit', 'TimetableController@edit')->name('timetable.edit');
    Route::put('/{id}', 'TimetableController@update')->name('timetable.update');
    Route::delete('/{id}', 'TimetableController@destroy')->name('timetable.destroy');
    Route::get('/{id}/print', 'TimetableController@print')->name('timetable.print');

    // Timetable Slots
    Route::get('/{id}/slots', 'TimetableController@slots')->name('timetable.slots');
    Route::post('/{id}/slots', 'TimetableController@storeSlot')->name('timetable.slots.store');
    Route::get('/{id}/slots/{slotId}/edit', 'TimetableController@editSlot')->name('timetable.slots.edit');
    Route::put('/{id}/slots/{slotId}', 'TimetableController@updateSlot')->name('timetable.slots.update');
    Route::delete('/{id}/slots/{slotId}', 'TimetableController@destroySlot')->name('timetable.slots.destroy');

    // Rooms
    Route::get('/rooms/index', 'TimetableController@rooms')->name('timetable.rooms');
    Route::get('/rooms/create', 'TimetableController@createRoom')->name('timetable.rooms.create');
    Route::post('/rooms', 'TimetableController@storeRoom')->name('timetable.rooms.store');
    Route::get('/rooms/{id}/edit', 'TimetableController@editRoom')->name('timetable.rooms.edit');
    Route::put('/rooms/{id}', 'TimetableController@updateRoom')->name('timetable.rooms.update');
    Route::delete('/rooms/{id}', 'TimetableController@destroyRoom')->name('timetable.rooms.destroy');

    // Teacher Timetable
    Route::get('/teacher-timetable', 'TimetableController@teacherTimetable')->name('timetable.teacher');
});

// Communication Routes
Route::group(['namespace' => 'Backend', 'middleware' => ['auth', 'module:communication'], 'prefix' => 'communication'], function () {
    // Messages
    Route::get('/inbox', 'CommunicationController@inbox')->name('communication.inbox');
    Route::get('/sent', 'CommunicationController@sent')->name('communication.sent');
    Route::get('/compose', 'CommunicationController@compose')->name('communication.compose');
    Route::post('/message', 'CommunicationController@storeMessage')->name('communication.message.store');
    Route::get('/message/{id}', 'CommunicationController@viewMessage')->name('communication.view');
    Route::delete('/message/{id}', 'CommunicationController@deleteMessage')->name('communication.message.delete');
    Route::post('/get-users-by-role', 'CommunicationController@getUsersByRole')->name('communication.get_users_by_role');

    // Notifications
    Route::get('/notifications', 'CommunicationController@notifications')->name('communication.notifications');
    Route::post('/notifications/{id}/read', 'CommunicationController@markNotificationAsRead')->name('communication.notification.read');
    Route::delete('/notifications/{id}', 'CommunicationController@deleteNotification')->name('communication.notification.delete');

    // Announcements
    Route::get('/announcements', 'CommunicationController@announcements')->name('communication.announcements');
    Route::get('/announcements/create', 'CommunicationController@createAnnouncement')->name('communication.announcements.create');
    Route::post('/announcements', 'CommunicationController@storeAnnouncement')->name('communication.announcements.store');
    Route::get('/announcements/{id}', 'CommunicationController@showAnnouncement')->name('communication.announcements.show');
    Route::get('/announcements/{id}/edit', 'CommunicationController@editAnnouncement')->name('communication.announcements.edit');
    Route::put('/announcements/{id}', 'CommunicationController@updateAnnouncement')->name('communication.announcements.update');
    Route::delete('/announcements/{id}', 'CommunicationController@destroyAnnouncement')->name('communication.announcements.destroy');

    // Email Templates
    Route::get('/email-templates', 'CommunicationController@emailTemplates')->name('communication.email_templates');
    Route::get('/email-templates/create', 'CommunicationController@createEmailTemplate')->name('communication.email_templates.create');
    Route::post('/email-templates', 'CommunicationController@storeEmailTemplate')->name('communication.email_templates.store');
    Route::get('/email-templates/{id}/edit', 'CommunicationController@editEmailTemplate')->name('communication.email_templates.edit');
    Route::put('/email-templates/{id}', 'CommunicationController@updateEmailTemplate')->name('communication.email_templates.update');
    Route::delete('/email-templates/{id}', 'CommunicationController@destroyEmailTemplate')->name('communication.email_templates.destroy');

    // SMS Templates
    Route::get('/sms-templates', 'CommunicationController@smsTemplates')->name('communication.sms_templates');
    Route::get('/sms-templates/create', 'CommunicationController@createSmsTemplate')->name('communication.sms_templates.create');
    Route::post('/sms-templates', 'CommunicationController@storeSmsTemplate')->name('communication.sms_templates.store');
    Route::get('/sms-templates/{id}/edit', 'CommunicationController@editSmsTemplate')->name('communication.sms_templates.edit');
    Route::put('/sms-templates/{id}', 'CommunicationController@updateSmsTemplate')->name('communication.sms_templates.update');
    Route::delete('/sms-templates/{id}', 'CommunicationController@destroySmsTemplate')->name('communication.sms_templates.destroy');

    // Bulk Email
    Route::get('/bulk-email', 'CommunicationController@bulkEmail')->name('communication.bulk_email');
    Route::post('/bulk-email', 'CommunicationController@sendBulkEmail')->name('communication.bulk_email.send');

    // Bulk SMS
    Route::get('/bulk-sms', 'CommunicationController@bulkSms')->name('communication.bulk_sms');
    Route::post('/bulk-sms', 'CommunicationController@sendBulkSms')->name('communication.bulk_sms.send');

    // Logs
    Route::get('/email-logs', 'CommunicationController@emailLogs')->name('communication.email_logs');
    Route::get('/sms-logs', 'CommunicationController@smsLogs')->name('communication.sms_logs');
});

// Advanced Reporting Routes
Route::group(['namespace' => 'Backend', 'middleware' => ['auth', 'module:advanced_reporting'], 'prefix' => 'reporting'], function () {
    // Dashboard
    Route::get('/dashboard', 'ReportingController@dashboard')->name('reporting.dashboard');

    // Student Reports
    Route::get('/students', 'ReportingController@studentReports')->name('reporting.students');
    Route::post('/students/list', 'ReportingController@generateStudentList')->name('reporting.students.list');

    // Attendance Reports
    Route::get('/attendance', 'ReportingController@attendanceReports')->name('reporting.attendance');
    Route::post('/attendance/report', 'ReportingController@generateAttendanceReport')->name('reporting.attendance.report');

    // Exam Reports
    Route::get('/exams', 'ReportingController@examReports')->name('reporting.exams');
    Route::post('/exams/report', 'ReportingController@generateExamReport')->name('reporting.exams.report');

    // Financial Reports
    Route::get('/financial', 'ReportingController@financialReports')->name('reporting.financial');
    Route::post('/financial/report', 'ReportingController@generateFinancialReport')->name('reporting.financial.report');

    // Library Reports
    Route::get('/library', 'ReportingController@libraryReports')->name('reporting.library');
    Route::post('/library/report', 'ReportingController@generateLibraryReport')->name('reporting.library.report');

    // Custom Reports
    Route::get('/custom', 'ReportingController@customReports')->name('reporting.custom');
    Route::post('/custom/report', 'ReportingController@generateCustomReport')->name('reporting.custom.report');
});

// Library Management Routes
Route::group(['namespace' => 'Backend', 'middleware' => ['auth', 'module:library'], 'prefix' => 'library'], function () {
    // Books
    Route::get('/books', 'LibraryController@books')->name('library.books');
    Route::get('/books/create', 'LibraryController@createBook')->name('library.books.create');
    Route::post('/books', 'LibraryController@storeBook')->name('library.books.store');
    Route::get('/books/{id}', 'LibraryController@showBook')->name('library.books.show');
    Route::get('/books/{id}/edit', 'LibraryController@editBook')->name('library.books.edit');
    Route::put('/books/{id}', 'LibraryController@updateBook')->name('library.books.update');
    Route::delete('/books/{id}', 'LibraryController@destroyBook')->name('library.books.destroy');

    // Categories
    Route::get('/categories', 'LibraryController@categories')->name('library.categories');
    Route::get('/categories/create', 'LibraryController@createCategory')->name('library.categories.create');
    Route::post('/categories', 'LibraryController@storeCategory')->name('library.categories.store');
    Route::get('/categories/{id}/edit', 'LibraryController@editCategory')->name('library.categories.edit');
    Route::put('/categories/{id}', 'LibraryController@updateCategory')->name('library.categories.update');
    Route::delete('/categories/{id}', 'LibraryController@destroyCategory')->name('library.categories.destroy');

    // Issues
    Route::get('/issues', 'LibraryController@issues')->name('library.issues');
    Route::get('/issues/create', 'LibraryController@createIssue')->name('library.issues.create');
    Route::post('/issues', 'LibraryController@storeIssue')->name('library.issues.store');
    Route::get('/issues/{id}', 'LibraryController@showIssue')->name('library.issues.show');
    Route::get('/issues/{id}/return', 'LibraryController@returnBook')->name('library.issues.return');
    Route::post('/issues/{id}/return', 'LibraryController@processReturn')->name('library.issues.process_return');
    Route::post('/issues/{id}/lost', 'LibraryController@markLost')->name('library.issues.mark_lost');

    // Settings
    Route::get('/settings', 'LibraryController@settings')->name('library.settings');
    Route::post('/settings', 'LibraryController@updateSettings')->name('library.settings.update');

    // Reports
    Route::get('/fine-collection', 'LibraryController@fineCollection')->name('library.fine_collection');
    Route::get('/overdue-books', 'LibraryController@overdueBooks')->name('library.overdue_books');

    // AJAX routes
    Route::post('/get-student-details', 'LibraryController@getStudentDetails')->name('library.get_student_details');
});

// Fee Management Routes
Route::group(['namespace' => 'Backend', 'middleware' => ['auth', 'module:student_billing'], 'prefix' => 'fee-management'], function () {
    // Fee Types
    Route::get('/fee-types', 'FeeManagementController@feeTypes')->name('fee_management.fee_types');
    Route::get('/fee-types/create', 'FeeManagementController@createFeeType')->name('fee_management.fee_types.create');
    Route::post('/fee-types', 'FeeManagementController@storeFeeType')->name('fee_management.fee_types.store');
    Route::get('/fee-types/{id}', 'FeeManagementController@showFeeType')->name('fee_management.fee_types.show');
    Route::get('/fee-types/{id}/edit', 'FeeManagementController@editFeeType')->name('fee_management.fee_types.edit');
    Route::put('/fee-types/{id}', 'FeeManagementController@updateFeeType')->name('fee_management.fee_types.update');
    Route::delete('/fee-types/{id}', 'FeeManagementController@destroyFeeType')->name('fee_management.fee_types.destroy');

    // Invoices
    Route::get('/invoices', 'FeeManagementController@invoices')->name('fee_management.invoices');
    Route::get('/invoices/create', 'FeeManagementController@createInvoice')->name('fee_management.invoices.create');
    Route::post('/invoices', 'FeeManagementController@storeInvoice')->name('fee_management.invoices.store');
    Route::get('/invoices/{id}', 'FeeManagementController@showInvoice')->name('fee_management.invoices.show');
    Route::get('/invoices/{id}/edit', 'FeeManagementController@editInvoice')->name('fee_management.invoices.edit');
    Route::put('/invoices/{id}', 'FeeManagementController@updateInvoice')->name('fee_management.invoices.update');
    Route::delete('/invoices/{id}', 'FeeManagementController@destroyInvoice')->name('fee_management.invoices.destroy');
    Route::get('/invoices/{id}/print', 'FeeManagementController@printInvoice')->name('fee_management.invoices.print');
    Route::post('/invoices/{id}/cancel', 'FeeManagementController@cancelInvoice')->name('fee_management.invoices.cancel');

    // Payments
    Route::get('/invoices/{id}/payments/add', 'FeeManagementController@addPayment')->name('fee_management.payments.add');
    Route::post('/invoices/{id}/payments', 'FeeManagementController@storePayment')->name('fee_management.payments.store');

    // AJAX routes
    Route::post('/get-class-details', 'FeeManagementController@getClassDetails')->name('fee_management.get_class_details');
    Route::post('/get-section-students', 'FeeManagementController@getSectionStudents')->name('fee_management.get_section_students');
    Route::post('/get-fee-type-details', 'FeeManagementController@getFeeTypeDetails')->name('fee_management.get_fee_type_details');

    // Generate recurring invoices
    Route::post('/generate-recurring-invoices', 'FeeManagementController@generateRecurringInvoices')->name('fee_management.generate_recurring_invoices');
});

// Online Learning Routes
Route::group(['namespace' => 'Backend', 'middleware' => ['auth', 'module:online_learning'], 'prefix' => 'online-learning'], function () {
    // Courses
    Route::get('/courses', 'OnlineLearningController@courses')->name('online_learning.courses');
    Route::get('/courses/create', 'OnlineLearningController@createCourse')->name('online_learning.courses.create');
    Route::post('/courses', 'OnlineLearningController@storeCourse')->name('online_learning.courses.store');
    Route::get('/courses/{id}', 'OnlineLearningController@showCourse')->name('online_learning.courses.show');
    Route::get('/courses/{id}/edit', 'OnlineLearningController@editCourse')->name('online_learning.courses.edit');
    Route::put('/courses/{id}', 'OnlineLearningController@updateCourse')->name('online_learning.courses.update');
    Route::delete('/courses/{id}', 'OnlineLearningController@destroyCourse')->name('online_learning.courses.destroy');
    Route::get('/courses/{id}/students', 'OnlineLearningController@manageStudents')->name('online_learning.courses.students');
    Route::post('/courses/{id}/students', 'OnlineLearningController@enrollStudents')->name('online_learning.courses.enroll');

    // Lessons
    Route::get('/courses/{courseId}/lessons', 'LessonController@index')->name('online_learning.lessons.index');
    Route::get('/courses/{courseId}/lessons/create', 'LessonController@create')->name('online_learning.lessons.create');
    Route::post('/courses/{courseId}/lessons', 'LessonController@store')->name('online_learning.lessons.store');
    Route::get('/courses/{courseId}/lessons/{id}', 'LessonController@show')->name('online_learning.lessons.show');
    Route::get('/courses/{courseId}/lessons/{id}/edit', 'LessonController@edit')->name('online_learning.lessons.edit');
    Route::put('/courses/{courseId}/lessons/{id}', 'LessonController@update')->name('online_learning.lessons.update');
    Route::delete('/courses/{courseId}/lessons/{id}', 'LessonController@destroy')->name('online_learning.lessons.destroy');
    Route::post('/courses/{courseId}/lessons/reorder', 'LessonController@reorder')->name('online_learning.lessons.reorder');
    Route::post('/courses/{courseId}/lessons/{lessonId}/resources', 'LessonController@addResource')->name('online_learning.lessons.resources.add');
    Route::delete('/courses/{courseId}/lessons/{lessonId}/resources/{resourceId}', 'LessonController@removeResource')->name('online_learning.lessons.resources.remove');

    // Assignments
    Route::get('/courses/{courseId}/assignments', 'AssignmentController@index')->name('online_learning.assignments.index');
    Route::get('/courses/{courseId}/assignments/create', 'AssignmentController@create')->name('online_learning.assignments.create');
    Route::post('/courses/{courseId}/assignments', 'AssignmentController@store')->name('online_learning.assignments.store');
    Route::get('/courses/{courseId}/assignments/{id}', 'AssignmentController@show')->name('online_learning.assignments.show');
    Route::get('/courses/{courseId}/assignments/{id}/edit', 'AssignmentController@edit')->name('online_learning.assignments.edit');
    Route::put('/courses/{courseId}/assignments/{id}', 'AssignmentController@update')->name('online_learning.assignments.update');
    Route::delete('/courses/{courseId}/assignments/{id}', 'AssignmentController@destroy')->name('online_learning.assignments.destroy');
    Route::get('/courses/{courseId}/assignments/{assignmentId}/submissions/{submissionId}', 'AssignmentController@viewSubmission')->name('online_learning.assignments.submissions.view');
    Route::post('/courses/{courseId}/assignments/{assignmentId}/submissions/{submissionId}/grade', 'AssignmentController@gradeSubmission')->name('online_learning.assignments.submissions.grade');
});

// Parent Portal Routes
Route::group(['namespace' => 'Backend', 'middleware' => ['auth', 'parent', 'module:parent_portal'], 'prefix' => 'parent-portal'], function () {
    // Dashboard
    Route::get('/dashboard', 'ParentPortalController@dashboard')
        ->name('parent.portal.dashboard');

    // Child Details
    Route::get('/child/{id}', 'ParentPortalController@childDetails')
        ->name('parent.portal.child_details');

    // Child Attendance
    Route::get('/child/{id}/attendance', 'ParentPortalController@childAttendance')
        ->name('parent.portal.child_attendance');

    // Child Grades
    Route::get('/child/{id}/grades', 'ParentPortalController@childGrades')
        ->name('parent.portal.child_grades');

    // Profile
    Route::get('/profile', 'ParentPortalController@profile')
        ->name('parent.portal.profile');
    Route::post('/profile/update', 'ParentPortalController@updateProfile')
        ->name('parent.portal.update_profile');

    // Change Password
    Route::get('/change-password', 'ParentPortalController@changePassword')
        ->name('parent.portal.change_password');
    Route::post('/change-password', 'ParentPortalController@changePassword')
        ->name('parent.portal.change_password');
});


//web artisan routes

//dev routes
Route::get('/make-link/{code}', function ($code) {
    //check access
    AppHelper::check_dev_route_access($code);

    //remove first
    if (is_link(public_path('storage'))) {
        unlink(public_path('storage'));
    }

    //create symbolic link for public image storage
    App::make('files')->link(storage_path('app/public'), public_path('storage'));
    return 'Done link';
});

Route::get('/cache-clear/{code}', function ($code) {
    //check access
    AppHelper::check_dev_route_access($code);

    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('route:clear');
    return 'clear cache';
});

//create triggers
Route::get('/create-triggers/{code}', function ($code) {
    //check access
    AppHelper::check_dev_route_access($code);

    AppHelper::createTriggers();
    return 'Triggers created :)';
});
