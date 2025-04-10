<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class ReportingService
{
    /**
     * Get student attendance report
     *
     * @param array $filters
     * @return array
     */
    public function getStudentAttendanceReport(array $filters)
    {
        try {
            $query = DB::table('student_attendances')
                ->join('students', 'student_attendances.student_id', '=', 'students.id')
                ->join('student_infos', 'students.id', '=', 'student_infos.student_id')
                ->join('academic_classes', 'student_attendances.class_id', '=', 'academic_classes.id')
                ->join('sections', 'student_attendances.section_id', '=', 'sections.id');

            // Apply filters
            if (isset($filters['class_id'])) {
                $query->where('student_attendances.class_id', $filters['class_id']);
            }
            
            if (isset($filters['section_id'])) {
                $query->where('student_attendances.section_id', $filters['section_id']);
            }
            
            if (isset($filters['date_from']) && isset($filters['date_to'])) {
                $query->whereBetween('student_attendances.attendance_date', [$filters['date_from'], $filters['date_to']]);
            } elseif (isset($filters['date_from'])) {
                $query->where('student_attendances.attendance_date', '>=', $filters['date_from']);
            } elseif (isset($filters['date_to'])) {
                $query->where('student_attendances.attendance_date', '<=', $filters['date_to']);
            }
            
            if (isset($filters['student_id'])) {
                $query->where('student_attendances.student_id', $filters['student_id']);
            }

            $data = $query->select(
                'students.id as student_id',
                'student_infos.name as student_name',
                'student_infos.roll_no',
                'academic_classes.name as class_name',
                'sections.name as section_name',
                DB::raw('COUNT(student_attendances.id) as total_days'),
                DB::raw('SUM(CASE WHEN student_attendances.present = 1 THEN 1 ELSE 0 END) as present_days'),
                DB::raw('SUM(CASE WHEN student_attendances.present = 0 THEN 1 ELSE 0 END) as absent_days')
            )
            ->groupBy('students.id', 'student_infos.name', 'student_infos.roll_no', 'academic_classes.name', 'sections.name')
            ->get();

            return [
                'data' => $data,
                'summary' => [
                    'total_students' => $data->count(),
                    'total_days' => $data->sum('total_days'),
                    'present_days' => $data->sum('present_days'),
                    'absent_days' => $data->sum('absent_days'),
                    'average_attendance' => $data->sum('total_days') > 0 
                        ? round(($data->sum('present_days') / $data->sum('total_days')) * 100, 2) 
                        : 0
                ]
            ];
        } catch (Exception $e) {
            Log::error('Error generating student attendance report: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return ['data' => [], 'summary' => []];
        }
    }

    /**
     * Get exam results report
     *
     * @param array $filters
     * @return array
     */
    public function getExamResultsReport(array $filters)
    {
        try {
            $query = DB::table('exam_results')
                ->join('students', 'exam_results.student_id', '=', 'students.id')
                ->join('student_infos', 'students.id', '=', 'student_infos.student_id')
                ->join('exams', 'exam_results.exam_id', '=', 'exams.id')
                ->join('academic_classes', 'exam_results.class_id', '=', 'academic_classes.id')
                ->join('sections', 'exam_results.section_id', '=', 'sections.id');

            // Apply filters
            if (isset($filters['exam_id'])) {
                $query->where('exam_results.exam_id', $filters['exam_id']);
            }
            
            if (isset($filters['class_id'])) {
                $query->where('exam_results.class_id', $filters['class_id']);
            }
            
            if (isset($filters['section_id'])) {
                $query->where('exam_results.section_id', $filters['section_id']);
            }
            
            if (isset($filters['student_id'])) {
                $query->where('exam_results.student_id', $filters['student_id']);
            }

            $data = $query->select(
                'students.id as student_id',
                'student_infos.name as student_name',
                'student_infos.roll_no',
                'exams.name as exam_name',
                'academic_classes.name as class_name',
                'sections.name as section_name',
                'exam_results.total_marks',
                'exam_results.obtained_marks',
                'exam_results.grade',
                'exam_results.remarks'
            )
            ->orderBy('exam_results.obtained_marks', 'desc')
            ->get();

            return [
                'data' => $data,
                'summary' => [
                    'total_students' => $data->count(),
                    'highest_marks' => $data->max('obtained_marks'),
                    'lowest_marks' => $data->min('obtained_marks'),
                    'average_marks' => $data->count() > 0 
                        ? round($data->sum('obtained_marks') / $data->count(), 2) 
                        : 0,
                    'pass_percentage' => $data->count() > 0 
                        ? round(($data->where('remarks', 'Pass')->count() / $data->count()) * 100, 2) 
                        : 0
                ]
            ];
        } catch (Exception $e) {
            Log::error('Error generating exam results report: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return ['data' => [], 'summary' => []];
        }
    }

    /**
     * Get fee collection report
     *
     * @param array $filters
     * @return array
     */
    public function getFeeCollectionReport(array $filters)
    {
        try {
            $query = DB::table('fee_payments')
                ->join('fee_invoices', 'fee_payments.invoice_id', '=', 'fee_invoices.id')
                ->join('students', 'fee_invoices.student_id', '=', 'students.id')
                ->join('student_infos', 'students.id', '=', 'student_infos.student_id')
                ->join('academic_classes', 'fee_invoices.class_id', '=', 'academic_classes.id')
                ->join('sections', 'fee_invoices.section_id', '=', 'sections.id')
                ->join('fee_types', 'fee_invoices.fee_type_id', '=', 'fee_types.id');

            // Apply filters
            if (isset($filters['date_from']) && isset($filters['date_to'])) {
                $query->whereBetween('fee_payments.payment_date', [$filters['date_from'], $filters['date_to']]);
            } elseif (isset($filters['date_from'])) {
                $query->where('fee_payments.payment_date', '>=', $filters['date_from']);
            } elseif (isset($filters['date_to'])) {
                $query->where('fee_payments.payment_date', '<=', $filters['date_to']);
            }
            
            if (isset($filters['class_id'])) {
                $query->where('fee_invoices.class_id', $filters['class_id']);
            }
            
            if (isset($filters['section_id'])) {
                $query->where('fee_invoices.section_id', $filters['section_id']);
            }
            
            if (isset($filters['fee_type_id'])) {
                $query->where('fee_invoices.fee_type_id', $filters['fee_type_id']);
            }

            $data = $query->select(
                'fee_payments.id as payment_id',
                'fee_payments.receipt_no',
                'fee_payments.payment_date',
                'fee_payments.amount',
                'fee_payments.payment_method',
                'fee_invoices.invoice_no',
                'students.id as student_id',
                'student_infos.name as student_name',
                'student_infos.roll_no',
                'academic_classes.name as class_name',
                'sections.name as section_name',
                'fee_types.name as fee_type'
            )
            ->orderBy('fee_payments.payment_date', 'desc')
            ->get();

            // Group by payment method
            $paymentMethodSummary = $data->groupBy('payment_method')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'amount' => $group->sum('amount')
                    ];
                });

            // Group by fee type
            $feeTypeSummary = $data->groupBy('fee_type')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'amount' => $group->sum('amount')
                    ];
                });

            return [
                'data' => $data,
                'summary' => [
                    'total_payments' => $data->count(),
                    'total_amount' => $data->sum('amount'),
                    'payment_methods' => $paymentMethodSummary,
                    'fee_types' => $feeTypeSummary
                ]
            ];
        } catch (Exception $e) {
            Log::error('Error generating fee collection report: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return ['data' => [], 'summary' => []];
        }
    }

    /**
     * Get student performance report
     *
     * @param int $studentId
     * @param int|null $academicYearId
     * @return array
     */
    public function getStudentPerformanceReport($studentId, $academicYearId = null)
    {
        try {
            // Get student info
            $student = DB::table('students')
                ->join('student_infos', 'students.id', '=', 'student_infos.student_id')
                ->where('students.id', $studentId)
                ->select('students.id', 'student_infos.name', 'student_infos.roll_no')
                ->first();

            if (!$student) {
                return ['student' => null, 'exams' => [], 'attendance' => [], 'summary' => []];
            }

            // Get exam results
            $examQuery = DB::table('exam_results')
                ->join('exams', 'exam_results.exam_id', '=', 'exams.id')
                ->where('exam_results.student_id', $studentId);

            if ($academicYearId) {
                $examQuery->where('exams.academic_year_id', $academicYearId);
            }

            $exams = $examQuery->select(
                'exams.name as exam_name',
                'exam_results.total_marks',
                'exam_results.obtained_marks',
                'exam_results.percentage',
                'exam_results.grade',
                'exam_results.remarks'
            )
            ->orderBy('exams.exam_date', 'asc')
            ->get();

            // Get attendance
            $attendanceQuery = DB::table('student_attendances')
                ->where('student_attendances.student_id', $studentId);

            if ($academicYearId) {
                $attendanceQuery->where('student_attendances.academic_year_id', $academicYearId);
            }

            $attendance = $attendanceQuery->select(
                DB::raw('MONTH(student_attendances.attendance_date) as month'),
                DB::raw('YEAR(student_attendances.attendance_date) as year'),
                DB::raw('COUNT(student_attendances.id) as total_days'),
                DB::raw('SUM(CASE WHEN student_attendances.present = 1 THEN 1 ELSE 0 END) as present_days'),
                DB::raw('SUM(CASE WHEN student_attendances.present = 0 THEN 1 ELSE 0 END) as absent_days')
            )
            ->groupBy(DB::raw('MONTH(student_attendances.attendance_date)'), DB::raw('YEAR(student_attendances.attendance_date)'))
            ->orderBy(DB::raw('YEAR(student_attendances.attendance_date)'), 'asc')
            ->orderBy(DB::raw('MONTH(student_attendances.attendance_date)'), 'asc')
            ->get();

            // Calculate summary
            $totalDays = $attendance->sum('total_days');
            $presentDays = $attendance->sum('present_days');
            $averageMarks = $exams->count() > 0 ? $exams->sum('percentage') / $exams->count() : 0;

            return [
                'student' => $student,
                'exams' => $exams,
                'attendance' => $attendance,
                'summary' => [
                    'total_exams' => $exams->count(),
                    'average_marks' => round($averageMarks, 2),
                    'highest_marks' => $exams->max('percentage'),
                    'lowest_marks' => $exams->min('percentage'),
                    'attendance_percentage' => $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0,
                    'total_days' => $totalDays,
                    'present_days' => $presentDays,
                    'absent_days' => $attendance->sum('absent_days')
                ]
            ];
        } catch (Exception $e) {
            Log::error('Error generating student performance report: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return ['student' => null, 'exams' => [], 'attendance' => [], 'summary' => []];
        }
    }

    /**
     * Get teacher performance report
     *
     * @param int $teacherId
     * @param int|null $academicYearId
     * @return array
     */
    public function getTeacherPerformanceReport($teacherId, $academicYearId = null)
    {
        try {
            // Get teacher info
            $teacher = DB::table('employees')
                ->join('users', 'employees.user_id', '=', 'users.id')
                ->where('employees.id', $teacherId)
                ->select('employees.id', 'users.name', 'employees.employee_id')
                ->first();

            if (!$teacher) {
                return ['teacher' => null, 'classes' => [], 'attendance' => [], 'summary' => []];
            }

            // Get classes taught
            $classesQuery = DB::table('teacher_subjects')
                ->join('academic_classes', 'teacher_subjects.class_id', '=', 'academic_classes.id')
                ->join('sections', 'teacher_subjects.section_id', '=', 'sections.id')
                ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
                ->where('teacher_subjects.teacher_id', $teacherId);

            if ($academicYearId) {
                $classesQuery->where('teacher_subjects.academic_year_id', $academicYearId);
            }

            $classes = $classesQuery->select(
                'academic_classes.name as class_name',
                'sections.name as section_name',
                'subjects.name as subject_name'
            )
            ->get();

            // Get attendance
            $attendanceQuery = DB::table('employee_attendances')
                ->where('employee_attendances.employee_id', $teacherId);

            if ($academicYearId) {
                $attendanceQuery->where('employee_attendances.academic_year_id', $academicYearId);
            }

            $attendance = $attendanceQuery->select(
                DB::raw('MONTH(employee_attendances.attendance_date) as month'),
                DB::raw('YEAR(employee_attendances.attendance_date) as year'),
                DB::raw('COUNT(employee_attendances.id) as total_days'),
                DB::raw('SUM(CASE WHEN employee_attendances.present = 1 THEN 1 ELSE 0 END) as present_days'),
                DB::raw('SUM(CASE WHEN employee_attendances.present = 0 THEN 1 ELSE 0 END) as absent_days')
            )
            ->groupBy(DB::raw('MONTH(employee_attendances.attendance_date)'), DB::raw('YEAR(employee_attendances.attendance_date)'))
            ->orderBy(DB::raw('YEAR(employee_attendances.attendance_date)'), 'asc')
            ->orderBy(DB::raw('MONTH(employee_attendances.attendance_date)'), 'asc')
            ->get();

            // Get student performance in teacher's subjects
            $studentPerformanceQuery = DB::table('exam_results')
                ->join('exams', 'exam_results.exam_id', '=', 'exams.id')
                ->join('subjects', 'exam_results.subject_id', '=', 'subjects.id')
                ->join('teacher_subjects', function ($join) use ($teacherId) {
                    $join->on('subjects.id', '=', 'teacher_subjects.subject_id')
                        ->where('teacher_subjects.teacher_id', $teacherId);
                });

            if ($academicYearId) {
                $studentPerformanceQuery->where('exams.academic_year_id', $academicYearId);
            }

            $studentPerformance = $studentPerformanceQuery->select(
                'subjects.name as subject_name',
                DB::raw('AVG(exam_results.percentage) as average_percentage'),
                DB::raw('COUNT(exam_results.id) as student_count')
            )
            ->groupBy('subjects.name')
            ->get();

            // Calculate summary
            $totalDays = $attendance->sum('total_days');
            $presentDays = $attendance->sum('present_days');
            $averageStudentPerformance = $studentPerformance->count() > 0 
                ? $studentPerformance->sum('average_percentage') / $studentPerformance->count() 
                : 0;

            return [
                'teacher' => $teacher,
                'classes' => $classes,
                'attendance' => $attendance,
                'student_performance' => $studentPerformance,
                'summary' => [
                    'total_classes' => $classes->count(),
                    'attendance_percentage' => $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0,
                    'total_days' => $totalDays,
                    'present_days' => $presentDays,
                    'absent_days' => $attendance->sum('absent_days'),
                    'average_student_performance' => round($averageStudentPerformance, 2)
                ]
            ];
        } catch (Exception $e) {
            Log::error('Error generating teacher performance report: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return ['teacher' => null, 'classes' => [], 'attendance' => [], 'student_performance' => [], 'summary' => []];
        }
    }
}
