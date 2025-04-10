<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Registration;
use App\Student;
use App\Employee;
use App\IClass;
use App\Section;
use App\Subject;
use App\Attendance;
use App\Exam;
use App\Mark;
use App\FeeInvoice;
use App\FeePayment;
use App\BookIssue;
use App\AcademicYear;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

/**
 * ReportingController
 * 
 * This controller handles the advanced reporting module functionality.
 */
class ReportingController extends Controller
{
    /**
     * Display the reporting dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        // Get counts for dashboard
        $studentsCount = Registration::where('status', AppHelper::ACTIVE)->count();
        $teachersCount = Employee::where('role_id', AppHelper::EMP_TEACHER)->count();
        $classesCount = IClass::where('status', AppHelper::ACTIVE)->count();
        $feesCollected = FeePayment::where('status', 'completed')
            ->whereYear('payment_date', date('Y'))
            ->sum('amount');
            
        // Get monthly fee collection for current year
        $monthlyFeeCollection = FeePayment::select(
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'completed')
            ->whereYear('payment_date', date('Y'))
            ->groupBy(DB::raw('MONTH(payment_date)'))
            ->orderBy('month')
            ->get();
            
        // Format data for chart
        $months = [];
        $collections = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('F', mktime(0, 0, 0, $i, 1));
            $collections[] = $monthlyFeeCollection->where('month', $i)->first()->total ?? 0;
        }
        
        // Get gender distribution
        $genderDistribution = Student::select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->get();
            
        // Get class distribution
        $classDistribution = Registration::select('class_id', DB::raw('count(*) as total'))
            ->where('status', AppHelper::ACTIVE)
            ->groupBy('class_id')
            ->with('class')
            ->get();
            
        return view('backend.reporting.dashboard', compact(
            'studentsCount', 
            'teachersCount', 
            'classesCount', 
            'feesCollected',
            'months',
            'collections',
            'genderDistribution',
            'classDistribution'
        ));
    }

    /**
     * Display the student reports page.
     *
     * @return \Illuminate\Http\Response
     */
    public function studentReports()
    {
        $classes = IClass::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        $academicYears = AcademicYear::orderBy('id', 'desc')->pluck('title', 'id');
        
        return view('backend.reporting.student_reports', compact('classes', 'academicYears'));
    }

    /**
     * Generate student list report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateStudentList(Request $request)
    {
        $this->validate($request, [
            'class_id' => 'nullable|integer|exists:i_classes,id',
            'section_id' => 'nullable|integer|exists:sections,id',
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'religion' => 'nullable|string',
            'report_type' => 'required|string|in:html,pdf,excel'
        ]);
        
        // Build query
        $query = Registration::with('student', 'class', 'section')
            ->where('academic_year_id', $request->academic_year_id)
            ->where('status', AppHelper::ACTIVE);
            
        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }
        
        if ($request->section_id) {
            $query->where('section_id', $request->section_id);
        }
        
        // Get students
        $students = $query->get();
        
        // Filter by gender and religion if provided
        if ($request->gender) {
            $students = $students->filter(function ($registration) use ($request) {
                return $registration->student->gender == $request->gender;
            });
        }
        
        if ($request->religion) {
            $students = $students->filter(function ($registration) use ($request) {
                return $registration->student->religion == $request->religion;
            });
        }
        
        // Get academic year
        $academicYear = AcademicYear::find($request->academic_year_id);
        
        // Get class and section names
        $className = $request->class_id ? IClass::find($request->class_id)->name : 'All Classes';
        $sectionName = $request->section_id ? Section::find($request->section_id)->name : 'All Sections';
        
        // Generate report based on type
        if ($request->report_type == 'pdf') {
            $pdf = PDF::loadView('backend.reporting.pdf.student_list', compact(
                'students', 
                'academicYear', 
                'className', 
                'sectionName',
                'request'
            ));
            
            return $pdf->download('student_list_' . date('Y-m-d') . '.pdf');
        } elseif ($request->report_type == 'excel') {
            // Excel export will be implemented later
            return redirect()->back()->with('error', 'Excel export is not implemented yet.');
        } else {
            return view('backend.reporting.html.student_list', compact(
                'students', 
                'academicYear', 
                'className', 
                'sectionName',
                'request'
            ));
        }
    }

    /**
     * Display the attendance reports page.
     *
     * @return \Illuminate\Http\Response
     */
    public function attendanceReports()
    {
        $classes = IClass::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        
        return view('backend.reporting.attendance_reports', compact('classes'));
    }

    /**
     * Generate attendance report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateAttendanceReport(Request $request)
    {
        $this->validate($request, [
            'class_id' => 'required|integer|exists:i_classes,id',
            'section_id' => 'required|integer|exists:sections,id',
            'month' => 'required|date_format:Y-m',
            'report_type' => 'required|string|in:html,pdf,excel'
        ]);
        
        // Parse month and year
        $month = date('m', strtotime($request->month));
        $year = date('Y', strtotime($request->month));
        
        // Get students
        $students = Registration::with('student')
            ->where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->where('status', AppHelper::ACTIVE)
            ->orderBy('roll_no', 'asc')
            ->get();
            
        // Get all days in the month
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $days = [];
        
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $days[] = Carbon::createFromDate($year, $month, $i);
        }
        
        // Get attendance records for each student
        foreach ($students as $student) {
            $attendanceRecords = Attendance::where('registration_id', $student->id)
                ->whereMonth('attendance_date', $month)
                ->whereYear('attendance_date', $year)
                ->get()
                ->keyBy(function ($attendance) {
                    return $attendance->attendance_date->day;
                });
                
            $student->attendance = $attendanceRecords;
            
            // Calculate statistics
            $student->present_count = $attendanceRecords->where('present', 1)->count();
            $student->absent_count = $attendanceRecords->where('present', 0)->count();
            $student->attendance_percentage = count($attendanceRecords) > 0 
                ? round(($student->present_count / count($attendanceRecords)) * 100, 2) 
                : 0;
        }
        
        // Get class and section
        $class = IClass::find($request->class_id);
        $section = Section::find($request->section_id);
        
        // Generate report based on type
        if ($request->report_type == 'pdf') {
            $pdf = PDF::loadView('backend.reporting.pdf.attendance_report', compact(
                'students', 
                'class', 
                'section',
                'days',
                'month',
                'year',
                'request'
            ));
            
            return $pdf->download('attendance_report_' . $request->month . '.pdf');
        } elseif ($request->report_type == 'excel') {
            // Excel export will be implemented later
            return redirect()->back()->with('error', 'Excel export is not implemented yet.');
        } else {
            return view('backend.reporting.html.attendance_report', compact(
                'students', 
                'class', 
                'section',
                'days',
                'month',
                'year',
                'request'
            ));
        }
    }

    /**
     * Display the exam reports page.
     *
     * @return \Illuminate\Http\Response
     */
    public function examReports()
    {
        $classes = IClass::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        $exams = Exam::orderBy('id', 'desc')->pluck('name', 'id');
        
        return view('backend.reporting.exam_reports', compact('classes', 'exams'));
    }

    /**
     * Generate exam report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateExamReport(Request $request)
    {
        $this->validate($request, [
            'class_id' => 'required|integer|exists:i_classes,id',
            'section_id' => 'required|integer|exists:sections,id',
            'exam_id' => 'required|integer|exists:exams,id',
            'report_type' => 'required|string|in:html,pdf,excel'
        ]);
        
        // Get students
        $students = Registration::with('student')
            ->where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->where('status', AppHelper::ACTIVE)
            ->orderBy('roll_no', 'asc')
            ->get();
            
        // Get subjects
        $subjects = Subject::where('class_id', $request->class_id)
            ->orderBy('name', 'asc')
            ->get();
            
        // Get exam
        $exam = Exam::find($request->exam_id);
        
        // Get marks for each student
        foreach ($students as $student) {
            $marks = Mark::where('registration_id', $student->id)
                ->where('exam_id', $request->exam_id)
                ->get()
                ->keyBy('subject_id');
                
            $student->marks = $marks;
            
            // Calculate total marks and percentage
            $totalMarks = $marks->sum('marks');
            $totalFullMarks = 0;
            
            foreach ($subjects as $subject) {
                $totalFullMarks += $subject->full_mark;
            }
            
            $student->total_marks = $totalMarks;
            $student->percentage = $totalFullMarks > 0 
                ? round(($totalMarks / $totalFullMarks) * 100, 2) 
                : 0;
                
            // Calculate grade
            if ($student->percentage >= 90) {
                $student->grade = 'A+';
            } elseif ($student->percentage >= 80) {
                $student->grade = 'A';
            } elseif ($student->percentage >= 70) {
                $student->grade = 'B+';
            } elseif ($student->percentage >= 60) {
                $student->grade = 'B';
            } elseif ($student->percentage >= 50) {
                $student->grade = 'C+';
            } elseif ($student->percentage >= 40) {
                $student->grade = 'C';
            } else {
                $student->grade = 'F';
            }
            
            // Check if passed
            $student->passed = true;
            foreach ($subjects as $subject) {
                if (!isset($marks[$subject->id]) || $marks[$subject->id]->marks < $subject->pass_mark) {
                    $student->passed = false;
                    break;
                }
            }
        }
        
        // Sort students by percentage
        $students = $students->sortByDesc('percentage');
        
        // Assign ranks
        $rank = 1;
        $prevPercentage = null;
        $prevRank = 1;
        
        foreach ($students as $student) {
            if ($prevPercentage !== null && $student->percentage < $prevPercentage) {
                $rank = $prevRank + 1;
            }
            
            $student->rank = $rank;
            $prevPercentage = $student->percentage;
            $prevRank = $rank;
            $rank++;
        }
        
        // Get class and section
        $class = IClass::find($request->class_id);
        $section = Section::find($request->section_id);
        
        // Generate report based on type
        if ($request->report_type == 'pdf') {
            $pdf = PDF::loadView('backend.reporting.pdf.exam_report', compact(
                'students', 
                'subjects',
                'exam',
                'class', 
                'section',
                'request'
            ));
            
            return $pdf->download('exam_report_' . $exam->name . '.pdf');
        } elseif ($request->report_type == 'excel') {
            // Excel export will be implemented later
            return redirect()->back()->with('error', 'Excel export is not implemented yet.');
        } else {
            return view('backend.reporting.html.exam_report', compact(
                'students', 
                'subjects',
                'exam',
                'class', 
                'section',
                'request'
            ));
        }
    }

    /**
     * Display the financial reports page.
     *
     * @return \Illuminate\Http\Response
     */
    public function financialReports()
    {
        return view('backend.reporting.financial_reports');
    }

    /**
     * Generate financial report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateFinancialReport(Request $request)
    {
        $this->validate($request, [
            'report_type' => 'required|string|in:income,expense,fee_collection,fee_due',
            'date_range' => 'required|string',
            'output_format' => 'required|string|in:html,pdf,excel'
        ]);
        
        // Parse date range
        $dates = explode(' - ', $request->date_range);
        $startDate = Carbon::createFromFormat('Y-m-d', $dates[0]);
        $endDate = Carbon::createFromFormat('Y-m-d', $dates[1]);
        
        // Generate report based on type
        if ($request->report_type == 'fee_collection') {
            // Get fee collections
            $collections = FeePayment::with('invoice.student.student', 'invoice.feeType')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->where('status', 'completed')
                ->orderBy('payment_date', 'desc')
                ->get();
                
            // Calculate total
            $total = $collections->sum('amount');
            
            // Group by fee type
            $byFeeType = $collections->groupBy(function ($payment) {
                return $payment->invoice->feeType->name;
            })->map(function ($payments) {
                return $payments->sum('amount');
            });
            
            // Group by payment method
            $byPaymentMethod = $collections->groupBy('payment_method')
                ->map(function ($payments) {
                    return $payments->sum('amount');
                });
                
            // Group by date
            $byDate = $collections->groupBy(function ($payment) {
                return $payment->payment_date->format('Y-m-d');
            })->map(function ($payments) {
                return $payments->sum('amount');
            });
            
            if ($request->output_format == 'pdf') {
                $pdf = PDF::loadView('backend.reporting.pdf.fee_collection', compact(
                    'collections', 
                    'total',
                    'byFeeType',
                    'byPaymentMethod',
                    'byDate',
                    'startDate',
                    'endDate',
                    'request'
                ));
                
                return $pdf->download('fee_collection_report_' . date('Y-m-d') . '.pdf');
            } elseif ($request->output_format == 'excel') {
                // Excel export will be implemented later
                return redirect()->back()->with('error', 'Excel export is not implemented yet.');
            } else {
                return view('backend.reporting.html.fee_collection', compact(
                    'collections', 
                    'total',
                    'byFeeType',
                    'byPaymentMethod',
                    'byDate',
                    'startDate',
                    'endDate',
                    'request'
                ));
            }
        } elseif ($request->report_type == 'fee_due') {
            // Get due invoices
            $invoices = FeeInvoice::with('student.student', 'feeType')
                ->where('due_date', '<=', $endDate)
                ->where('status', '!=', 'paid')
                ->where('status', '!=', 'cancelled')
                ->orderBy('due_date', 'asc')
                ->get();
                
            // Calculate total due
            $totalDue = $invoices->sum('due_amount');
            
            // Group by class
            $byClass = $invoices->groupBy(function ($invoice) {
                return $invoice->student->class->name;
            })->map(function ($invoices) {
                return $invoices->sum('due_amount');
            });
            
            // Group by fee type
            $byFeeType = $invoices->groupBy(function ($invoice) {
                return $invoice->feeType->name;
            })->map(function ($invoices) {
                return $invoices->sum('due_amount');
            });
            
            if ($request->output_format == 'pdf') {
                $pdf = PDF::loadView('backend.reporting.pdf.fee_due', compact(
                    'invoices', 
                    'totalDue',
                    'byClass',
                    'byFeeType',
                    'endDate',
                    'request'
                ));
                
                return $pdf->download('fee_due_report_' . date('Y-m-d') . '.pdf');
            } elseif ($request->output_format == 'excel') {
                // Excel export will be implemented later
                return redirect()->back()->with('error', 'Excel export is not implemented yet.');
            } else {
                return view('backend.reporting.html.fee_due', compact(
                    'invoices', 
                    'totalDue',
                    'byClass',
                    'byFeeType',
                    'endDate',
                    'request'
                ));
            }
        } else {
            return redirect()->back()->with('error', 'Report type not implemented yet.');
        }
    }

    /**
     * Display the library reports page.
     *
     * @return \Illuminate\Http\Response
     */
    public function libraryReports()
    {
        return view('backend.reporting.library_reports');
    }

    /**
     * Generate library report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateLibraryReport(Request $request)
    {
        $this->validate($request, [
            'report_type' => 'required|string|in:books_issued,books_overdue,fine_collection',
            'date_range' => 'required|string',
            'output_format' => 'required|string|in:html,pdf,excel'
        ]);
        
        // Parse date range
        $dates = explode(' - ', $request->date_range);
        $startDate = Carbon::createFromFormat('Y-m-d', $dates[0]);
        $endDate = Carbon::createFromFormat('Y-m-d', $dates[1]);
        
        // Generate report based on type
        if ($request->report_type == 'books_issued') {
            // Get book issues
            $issues = BookIssue::with('book.category', 'student.student', 'student.class', 'student.section')
                ->whereBetween('issue_date', [$startDate, $endDate])
                ->orderBy('issue_date', 'desc')
                ->get();
                
            // Calculate total
            $total = $issues->count();
            
            // Group by category
            $byCategory = $issues->groupBy(function ($issue) {
                return $issue->book->category->name;
            })->map(function ($issues) {
                return $issues->count();
            });
            
            // Group by class
            $byClass = $issues->groupBy(function ($issue) {
                return $issue->student->class->name;
            })->map(function ($issues) {
                return $issues->count();
            });
            
            if ($request->output_format == 'pdf') {
                $pdf = PDF::loadView('backend.reporting.pdf.books_issued', compact(
                    'issues', 
                    'total',
                    'byCategory',
                    'byClass',
                    'startDate',
                    'endDate',
                    'request'
                ));
                
                return $pdf->download('books_issued_report_' . date('Y-m-d') . '.pdf');
            } elseif ($request->output_format == 'excel') {
                // Excel export will be implemented later
                return redirect()->back()->with('error', 'Excel export is not implemented yet.');
            } else {
                return view('backend.reporting.html.books_issued', compact(
                    'issues', 
                    'total',
                    'byCategory',
                    'byClass',
                    'startDate',
                    'endDate',
                    'request'
                ));
            }
        } elseif ($request->report_type == 'books_overdue') {
            // Get overdue books
            $issues = BookIssue::with('book.category', 'student.student', 'student.class', 'student.section')
                ->where('status', '!=', 'returned')
                ->where('due_date', '<', now())
                ->orderBy('due_date', 'asc')
                ->get();
                
            // Calculate total
            $total = $issues->count();
            
            // Group by class
            $byClass = $issues->groupBy(function ($issue) {
                return $issue->student->class->name;
            })->map(function ($issues) {
                return $issues->count();
            });
            
            // Calculate days overdue
            foreach ($issues as $issue) {
                $issue->days_overdue = now()->diffInDays($issue->due_date);
            }
            
            if ($request->output_format == 'pdf') {
                $pdf = PDF::loadView('backend.reporting.pdf.books_overdue', compact(
                    'issues', 
                    'total',
                    'byClass',
                    'request'
                ));
                
                return $pdf->download('books_overdue_report_' . date('Y-m-d') . '.pdf');
            } elseif ($request->output_format == 'excel') {
                // Excel export will be implemented later
                return redirect()->back()->with('error', 'Excel export is not implemented yet.');
            } else {
                return view('backend.reporting.html.books_overdue', compact(
                    'issues', 
                    'total',
                    'byClass',
                    'request'
                ));
            }
        } else {
            return redirect()->back()->with('error', 'Report type not implemented yet.');
        }
    }

    /**
     * Display the custom reports page.
     *
     * @return \Illuminate\Http\Response
     */
    public function customReports()
    {
        return view('backend.reporting.custom_reports');
    }

    /**
     * Generate custom report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateCustomReport(Request $request)
    {
        // This will be implemented later
        return redirect()->back()->with('error', 'Custom reports are not implemented yet.');
    }
}
