<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use App\Registration;
use App\Attendance;
use App\Exam;
use App\Mark;
use App\Subject;
use App\FeeInvoice;
use App\BookIssue;
use App\Http\Helpers\AppHelper;

/**
 * StudentController
 *
 * This controller handles the student-related API endpoints.
 */
class StudentController extends BaseApiController
{
    /**
     * Get student profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        $student = Registration::with('student', 'class', 'section', 'academic_year')
            ->where('student_id', $user->id)
            ->first();

        if (!$student) {
            return $this->notFoundResponse('Student not found');
        }

        return $this->successResponse([
            'student' => [
                    'id' => $student->id,
                    'name' => $student->student->name,
                    'roll_no' => $student->roll_no,
                    'class' => $student->class->name,
                    'section' => $student->section->name,
                    'academic_year' => $student->academic_year->title,
                    'dob' => $student->student->dob,
                    'gender' => $student->student->gender,
                    'religion' => $student->student->religion,
                    'blood_group' => $student->student->blood_group,
                    'nationality' => $student->student->nationality,
                    'photo' => $student->student->photo ? asset('storage/student/' . $student->student->photo) : null,
                    'email' => $student->student->email,
                    'phone' => $student->student->phone_no,
                    'address' => $student->student->present_address,
                    'father_name' => $student->student->father_name,
                    'mother_name' => $student->student->mother_name,
                    'guardian_name' => $student->student->guardian,
                    'guardian_phone' => $student->student->guardian_phone_no
                ]
            ]
        ]);
    }

    /**
     * Get student attendance
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function attendance(Request $request)
    {
        $user = $request->user();

        $student = Registration::where('student_id', $user->id)->first();
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $month = $request->month ? date('m', strtotime($request->month)) : date('m');
        $year = $request->year ? $request->year : date('Y');

        $attendances = Attendance::where('registration_id', $student->id)
            ->whereMonth('attendance_date', $month)
            ->whereYear('attendance_date', $year)
            ->orderBy('attendance_date', 'asc')
            ->get();

        $attendanceData = [];
        foreach ($attendances as $attendance) {
            $attendanceData[] = [
                'date' => $attendance->attendance_date->format('Y-m-d'),
                'status' => $attendance->present ? 'Present' : 'Absent',
                'remarks' => $attendance->remark
            ];
        }

        // Calculate statistics
        $totalDays = count($attendanceData);
        $presentDays = $attendances->where('present', 1)->count();
        $absentDays = $totalDays - $presentDays;
        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'month' => date('F', strtotime($year . '-' . $month . '-01')),
                'year' => $year,
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'attendance_percentage' => $attendancePercentage,
                'attendances' => $attendanceData
            ]
        ]);
    }

    /**
     * Get student subjects
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function subjects(Request $request)
    {
        $user = $request->user();

        $student = Registration::where('student_id', $user->id)->first();
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $subjects = Subject::where('class_id', $student->class_id)
            ->with('teacher')
            ->get()
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'code' => $subject->code,
                    'type' => $subject->type,
                    'full_mark' => $subject->full_mark,
                    'pass_mark' => $subject->pass_mark,
                    'teacher' => $subject->teacher ? $subject->teacher->name : null
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
     * Get student exams and results
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function results(Request $request)
    {
        $user = $request->user();

        $student = Registration::where('student_id', $user->id)->first();
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $exams = Exam::where('class_id', $student->class_id)
            ->orWhere('class_id', 0)
            ->get();

        $examResults = [];
        foreach ($exams as $exam) {
            $marks = Mark::where('registration_id', $student->id)
                ->where('exam_id', $exam->id)
                ->with('subject')
                ->get();

            $subjectMarks = [];
            $totalMarks = 0;
            $totalFullMarks = 0;

            foreach ($marks as $mark) {
                $subjectMarks[] = [
                    'subject' => $mark->subject->name,
                    'full_mark' => $mark->subject->full_mark,
                    'pass_mark' => $mark->subject->pass_mark,
                    'marks' => $mark->marks,
                    'grade' => $mark->grade,
                    'point' => $mark->point,
                    'is_pass' => $mark->marks >= $mark->subject->pass_mark
                ];

                $totalMarks += $mark->marks;
                $totalFullMarks += $mark->subject->full_mark;
            }

            $percentage = $totalFullMarks > 0 ? round(($totalMarks / $totalFullMarks) * 100, 2) : 0;

            $examResults[] = [
                'exam_id' => $exam->id,
                'exam_name' => $exam->name,
                'total_marks' => $totalMarks,
                'total_full_marks' => $totalFullMarks,
                'percentage' => $percentage,
                'subjects' => $subjectMarks
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'results' => $examResults
            ]
        ]);
    }

    /**
     * Get student fee invoices
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function fees(Request $request)
    {
        $user = $request->user();

        $student = Registration::where('student_id', $user->id)->first();
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $invoices = FeeInvoice::where('student_id', $student->id)
            ->with('feeType', 'payments')
            ->orderBy('issue_date', 'desc')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_no' => $invoice->invoice_no,
                    'fee_type' => $invoice->feeType->name,
                    'amount' => $invoice->amount,
                    'discount' => $invoice->discount,
                    'fine' => $invoice->fine,
                    'total_amount' => $invoice->total_amount,
                    'paid_amount' => $invoice->paid_amount,
                    'due_amount' => $invoice->due_amount,
                    'issue_date' => $invoice->issue_date->format('Y-m-d'),
                    'due_date' => $invoice->due_date->format('Y-m-d'),
                    'status' => $invoice->status,
                    'is_overdue' => $invoice->isOverdue(),
                    'payments' => $invoice->payments->map(function ($payment) {
                        return [
                            'amount' => $payment->amount,
                            'payment_method' => $payment->payment_method_text,
                            'payment_date' => $payment->payment_date->format('Y-m-d'),
                            'status' => $payment->status
                        ];
                    })
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'invoices' => $invoices
            ]
        ]);
    }

    /**
     * Get student library books
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function books(Request $request)
    {
        $user = $request->user();

        $student = Registration::where('student_id', $user->id)->first();
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        $issues = BookIssue::where('student_id', $student->id)
            ->with('book.category')
            ->orderBy('issue_date', 'desc')
            ->get()
            ->map(function ($issue) {
                return [
                    'id' => $issue->id,
                    'book' => [
                        'id' => $issue->book->id,
                        'title' => $issue->book->title,
                        'author' => $issue->book->author,
                        'category' => $issue->book->category->name,
                        'image' => $issue->book->image ? asset('storage/books/' . $issue->book->image) : null
                    ],
                    'issue_date' => $issue->issue_date->format('Y-m-d'),
                    'due_date' => $issue->due_date->format('Y-m-d'),
                    'return_date' => $issue->return_date ? $issue->return_date->format('Y-m-d') : null,
                    'status' => $issue->status,
                    'is_overdue' => $issue->isOverdue(),
                    'fine_amount' => $issue->fine_amount,
                    'fine_paid' => $issue->fine_paid
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'issues' => $issues
            ]
        ]);
    }
}
