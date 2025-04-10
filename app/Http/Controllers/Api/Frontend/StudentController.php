<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Api\Frontend\BaseFrontendApiController;
use App\Http\Resources\StudentResource;
use App\Services\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends BaseFrontendApiController
{
    /**
     * @var StudentService
     */
    protected $studentService;

    /**
     * StudentController constructor.
     *
     * @param StudentService $studentService
     */
    public function __construct(StudentService $studentService)
    {
        $this->middleware('auth:api');
        $this->studentService = $studentService;
    }

    /**
     * Get the authenticated student's profile.
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function profile()
    {
        $user = Auth::user();
        $student = $this->studentService->findByField('user_id', $user->id);

        if (!$student) {
            return $this->notFoundResponse('Student profile not found');
        }

        return $this->resourceResponse(new StudentResource($student), 'Student profile retrieved successfully');
    }

    /**
     * Update the authenticated student's profile.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $student = $this->studentService->findByField('user_id', $user->id);

        if (!$student) {
            return $this->notFoundResponse('Student profile not found');
        }

        // Validate request
        $request->validate([
            'phone_no' => 'nullable|string|max:20',
            'present_address' => 'nullable|string|max:500',
            'permanent_address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Only allow updating certain fields
        $data = $request->only([
            'phone_no',
            'present_address',
            'permanent_address',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = uniqid('student_') . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('public/photos/students', $filename);
            $data['photo'] = $filename;
        }

        $student = $this->studentService->update($data, $student->id);

        if (!$student) {
            return $this->serverErrorResponse('Failed to update profile');
        }

        return $this->resourceResponse(new StudentResource($student), 'Profile updated successfully');
    }

    /**
     * Get the authenticated student's attendance.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function attendance(Request $request)
    {
        $user = Auth::user();
        $student = $this->studentService->findByField('user_id', $user->id);

        if (!$student) {
            return $this->notFoundResponse('Student profile not found');
        }

        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $attendance = $this->studentService->getStudentAttendance($student->id, $month, $year);

        return $this->successResponse($attendance, 'Attendance retrieved successfully');
    }

    /**
     * Get the authenticated student's results.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function results(Request $request)
    {
        $user = Auth::user();
        $student = $this->studentService->findByField('user_id', $user->id);

        if (!$student) {
            return $this->notFoundResponse('Student profile not found');
        }

        $examId = $request->input('exam_id');

        if (!$examId) {
            return $this->validationErrorResponse(['exam_id' => ['Exam ID is required']]);
        }

        $results = $this->studentService->getStudentResults($student->id, $examId);

        return $this->successResponse($results, 'Results retrieved successfully');
    }

    /**
     * Get the authenticated student's timetable.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function timetable()
    {
        $user = Auth::user();
        $student = $this->studentService->findByField('user_id', $user->id);

        if (!$student) {
            return $this->notFoundResponse('Student profile not found');
        }

        $registration = $student->registration()->where('is_promoted', 0)->first();

        if (!$registration) {
            return $this->notFoundResponse('No active registration found');
        }

        $timetable = $this->studentService->getStudentTimetable($registration->class_id, $registration->section_id);

        return $this->successResponse($timetable, 'Timetable retrieved successfully');
    }
}
