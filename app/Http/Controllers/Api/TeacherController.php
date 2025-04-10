<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Employee;
use App\Subject;
use App\IClass;
use App\Section;
use App\Registration;
use App\Attendance;
use App\Exam;
use App\Mark;
use App\Http\Helpers\AppHelper;
use Carbon\Carbon;

/**
 * TeacherController
 * 
 * This controller handles the teacher-related API endpoints.
 */
class TeacherController extends Controller
{
    /**
     * Get teacher profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        $teacher = Employee::with('role')
            ->where('id', $user->id)
            ->first();
            
        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'teacher' => [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'designation' => $teacher->designation,
                    'employee_id' => $teacher->employee_id,
                    'role' => $teacher->role->name,
                    'dob' => $teacher->dob,
                    'gender' => $teacher->gender,
                    'religion' => $teacher->religion,
                    'photo' => $teacher->photo ? asset('storage/employee/' . $teacher->photo) : null,
                    'email' => $teacher->email,
                    'phone' => $teacher->phone_no,
                    'address' => $teacher->address,
                    'joining_date' => $teacher->joining_date,
                    'qualification' => $teacher->qualification
                ]
            ]
        ]);
    }

    /**
     * Get teacher subjects
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function subjects(Request $request)
    {
        $user = $request->user();
        
        $subjects = Subject::where('teacher_id', $user->id)
            ->with('class')
            ->get()
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'code' => $subject->code,
                    'class' => $subject->class->name,
                    'type' => $subject->type,
                    'full_mark' => $subject->full_mark,
                    'pass_mark' => $subject->pass_mark
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'subjects' => $subjects
            ]
        ]);
    }

    /**
     * Get classes and sections
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function classes(Request $request)
    {
        $user = $request->user();
        
        // Get classes where teacher teaches at least one subject
        $classIds = Subject::where('teacher_id', $user->id)
            ->pluck('class_id')
            ->unique();
            
        $classes = IClass::whereIn('id', $classIds)
            ->with('sections')
            ->get()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'numeric_value' => $class->numeric_value,
                    'sections' => $class->sections->map(function ($section) {
                        return [
                            'id' => $section->id,
                            'name' => $section->name,
                            'capacity' => $section->capacity,
                            'students_count' => Registration::where('section_id', $section->id)
                                ->where('status', AppHelper::ACTIVE)
                                ->count()
                        ];
                    })
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'classes' => $classes
            ]
        ]);
    }

    /**
     * Get students by class and section
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function students(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'class_id' => 'required|integer|exists:i_classes,id',
            'section_id' => 'required|integer|exists:sections,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $students = Registration::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->where('status', AppHelper::ACTIVE)
            ->with('student')
            ->orderBy('roll_no', 'asc')
            ->get()
            ->map(function ($registration) {
                return [
                    'id' => $registration->id,
                    'name' => $registration->student->name,
                    'roll_no' => $registration->roll_no,
                    'gender' => $registration->student->gender,
                    'photo' => $registration->student->photo ? asset('storage/student/' . $registration->student->photo) : null,
                    'email' => $registration->student->email,
                    'phone' => $registration->student->phone_no
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'students' => $students
            ]
        ]);
    }

    /**
     * Get attendance by class and section
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function attendance(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'class_id' => 'required|integer|exists:i_classes,id',
            'section_id' => 'required|integer|exists:sections,id',
            'date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $date = Carbon::parse($request->date);
        
        $students = Registration::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->where('status', AppHelper::ACTIVE)
            ->with(['student', 'attendances' => function ($query) use ($date) {
                $query->whereDate('attendance_date', $date);
            }])
            ->orderBy('roll_no', 'asc')
            ->get()
            ->map(function ($registration) {
                $attendance = $registration->attendances->first();
                
                return [
                    'id' => $registration->id,
                    'name' => $registration->student->name,
                    'roll_no' => $registration->roll_no,
                    'attendance' => [
                        'id' => $attendance ? $attendance->id : null,
                        'present' => $attendance ? $attendance->present : null,
                        'remark' => $attendance ? $attendance->remark : null
                    ]
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date->format('Y-m-d'),
                'students' => $students
            ]
        ]);
    }

    /**
     * Save attendance
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveAttendance(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'class_id' => 'required|integer|exists:i_classes,id',
            'section_id' => 'required|integer|exists:sections,id',
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|integer|exists:registrations,id',
            'attendances.*.present' => 'required|boolean',
            'attendances.*.remark' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $date = Carbon::parse($request->date);
        
        foreach ($request->attendances as $attendanceData) {
            $attendance = Attendance::updateOrCreate(
                [
                    'registration_id' => $attendanceData['student_id'],
                    'attendance_date' => $date
                ],
                [
                    'present' => $attendanceData['present'],
                    'remark' => $attendanceData['remark'] ?? null
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance saved successfully'
        ]);
    }

    /**
     * Get exams by class
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exams(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'class_id' => 'required|integer|exists:i_classes,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $exams = Exam::where(function ($query) use ($request) {
                $query->where('class_id', $request->class_id)
                    ->orWhere('class_id', 0);
            })
            ->get()
            ->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'name' => $exam->name,
                    'start_date' => $exam->start_date,
                    'end_date' => $exam->end_date,
                    'result_date' => $exam->result_date,
                    'status' => $exam->status
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'exams' => $exams
            ]
        ]);
    }

    /**
     * Get marks entry form
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function marksForm(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'exam_id' => 'required|integer|exists:exams,id',
            'class_id' => 'required|integer|exists:i_classes,id',
            'section_id' => 'required|integer|exists:sections,id',
            'subject_id' => 'required|integer|exists:subjects,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = $request->user();
        
        // Check if subject belongs to teacher
        $subject = Subject::where('id', $request->subject_id)
            ->where('teacher_id', $user->id)
            ->first();
            
        if (!$subject) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to enter marks for this subject'
            ], 403);
        }
        
        $students = Registration::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->where('status', AppHelper::ACTIVE)
            ->with(['student', 'marks' => function ($query) use ($request) {
                $query->where('exam_id', $request->exam_id)
                    ->where('subject_id', $request->subject_id);
            }])
            ->orderBy('roll_no', 'asc')
            ->get()
            ->map(function ($registration) use ($subject) {
                $mark = $registration->marks->first();
                
                return [
                    'id' => $registration->id,
                    'name' => $registration->student->name,
                    'roll_no' => $registration->roll_no,
                    'mark' => [
                        'id' => $mark ? $mark->id : null,
                        'marks' => $mark ? $mark->marks : null,
                        'grade' => $mark ? $mark->grade : null,
                        'point' => $mark ? $mark->point : null,
                        'comment' => $mark ? $mark->comment : null
                    ],
                    'full_mark' => $subject->full_mark,
                    'pass_mark' => $subject->pass_mark
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'students' => $students
            ]
        ]);
    }

    /**
     * Save marks
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveMarks(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'exam_id' => 'required|integer|exists:exams,id',
            'class_id' => 'required|integer|exists:i_classes,id',
            'section_id' => 'required|integer|exists:sections,id',
            'subject_id' => 'required|integer|exists:subjects,id',
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|integer|exists:registrations,id',
            'marks.*.marks' => 'required|numeric|min:0',
            'marks.*.comment' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = $request->user();
        
        // Check if subject belongs to teacher
        $subject = Subject::where('id', $request->subject_id)
            ->where('teacher_id', $user->id)
            ->first();
            
        if (!$subject) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to enter marks for this subject'
            ], 403);
        }
        
        // Get grade system
        $exam = Exam::find($request->exam_id);
        $gradeSystem = $exam->grade_system;
        
        foreach ($request->marks as $markData) {
            // Calculate grade and point
            $marks = $markData['marks'];
            $grade = '';
            $point = 0;
            
            if ($gradeSystem) {
                foreach ($gradeSystem->rules as $rule) {
                    if ($marks >= $rule->marks_from && $marks <= $rule->marks_to) {
                        $grade = $rule->grade;
                        $point = $rule->point;
                        break;
                    }
                }
            }
            
            $mark = Mark::updateOrCreate(
                [
                    'registration_id' => $markData['student_id'],
                    'exam_id' => $request->exam_id,
                    'subject_id' => $request->subject_id
                ],
                [
                    'marks' => $marks,
                    'grade' => $grade,
                    'point' => $point,
                    'comment' => $markData['comment'] ?? null
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Marks saved successfully'
        ]);
    }
}
