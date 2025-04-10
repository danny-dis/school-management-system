<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Registration;
use App\Student;
use App\StudentAttendance;
use App\Mark;
use App\Result;
use App\Exam;
use App\Subject;
use App\AppHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * ParentPortalController - Handles parent portal functionality
 * 
 * This controller provides functionality for parents to view their
 * children's information, including attendance, grades, and more.
 * 
 * @package App\Http\Controllers\Backend
 * @author Zophlic Development Team
 */
class ParentPortalController extends Controller
{
    /**
     * Display parent dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        // Get the authenticated parent
        $user = Auth::user();
        
        // Get children of this parent
        $children = Student::where('guardian_phone_no', $user->phone_no)
            ->orWhere('father_phone_no', $user->phone_no)
            ->orWhere('mother_phone_no', $user->phone_no)
            ->get();
        
        if ($children->isEmpty()) {
            return redirect()->route('user.dashboard')->with('error', 'No children found associated with your account!');
        }
        
        // Get the current registrations for all children
        $registrations = Registration::whereIn('student_id', $children->pluck('id'))
            ->where('status', AppHelper::ACTIVE)
            ->with('student', 'class', 'section', 'academic_year')
            ->get();
            
        if ($registrations->isEmpty()) {
            return redirect()->route('user.dashboard')->with('error', 'No active registrations found for your children!');
        }
        
        // Get recent attendance for all children
        $recentAttendances = [];
        foreach ($registrations as $registration) {
            $recentAttendances[$registration->id] = StudentAttendance::where('registration_id', $registration->id)
                ->orderBy('attendance_date', 'desc')
                ->take(5)
                ->get();
        }
        
        // Get recent exams
        $recentExams = Exam::where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();
        
        return view('backend.parent.portal.dashboard', compact(
            'children',
            'registrations',
            'recentAttendances',
            'recentExams',
            'user'
        ));
    }
    
    /**
     * Display child details
     *
     * @param int $id Student ID
     * @return \Illuminate\Http\Response
     */
    public function childDetails($id)
    {
        // Get the authenticated parent
        $user = Auth::user();
        
        // Get the child
        $student = Student::findOrFail($id);
        
        // Check if this is the parent's child
        if ($student->guardian_phone_no != $user->phone_no && 
            $student->father_phone_no != $user->phone_no && 
            $student->mother_phone_no != $user->phone_no) {
            return redirect()->route('parent.portal.dashboard')->with('error', 'You do not have permission to view this student!');
        }
        
        // Get the current registration info
        $registration = Registration::where('student_id', $student->id)
            ->where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->first();
            
        if (!$registration) {
            return redirect()->route('parent.portal.dashboard')->with('error', 'No active registration found for this student!');
        }
        
        // Get attendance records
        $attendances = StudentAttendance::where('registration_id', $registration->id)
            ->orderBy('attendance_date', 'desc')
            ->paginate(10);
            
        // Calculate attendance statistics
        $totalDays = StudentAttendance::where('registration_id', $registration->id)->count();
        $presentDays = StudentAttendance::where('registration_id', $registration->id)
            ->where('present', 1)
            ->count();
        $absentDays = $totalDays - $presentDays;
        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;
        
        // Get exams
        $exams = Exam::where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->get();
            
        // Get results for the student
        $results = Result::where('registration_id', $registration->id)
            ->orderBy('id', 'desc')
            ->get();
            
        // Get marks for the student
        $marks = Mark::where('registration_id', $registration->id)
            ->orderBy('id', 'desc')
            ->get();
        
        // Get subjects for the student
        $subjects = Subject::whereHas('students', function ($query) use ($registration) {
            $query->where('registration_id', $registration->id);
        })->get();
        
        return view('backend.parent.portal.child_details', compact(
            'student',
            'registration',
            'attendances',
            'totalDays',
            'presentDays',
            'absentDays',
            'attendancePercentage',
            'exams',
            'results',
            'marks',
            'subjects'
        ));
    }
    
    /**
     * Display child attendance
     *
     * @param int $id Student ID
     * @return \Illuminate\Http\Response
     */
    public function childAttendance($id)
    {
        // Get the authenticated parent
        $user = Auth::user();
        
        // Get the child
        $student = Student::findOrFail($id);
        
        // Check if this is the parent's child
        if ($student->guardian_phone_no != $user->phone_no && 
            $student->father_phone_no != $user->phone_no && 
            $student->mother_phone_no != $user->phone_no) {
            return redirect()->route('parent.portal.dashboard')->with('error', 'You do not have permission to view this student!');
        }
        
        // Get the current registration info
        $registration = Registration::where('student_id', $student->id)
            ->where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->first();
            
        if (!$registration) {
            return redirect()->route('parent.portal.dashboard')->with('error', 'No active registration found for this student!');
        }
        
        // Get all attendance records
        $attendances = StudentAttendance::where('registration_id', $registration->id)
            ->orderBy('attendance_date', 'desc')
            ->paginate(15);
            
        // Calculate attendance statistics
        $totalDays = StudentAttendance::where('registration_id', $registration->id)->count();
        $presentDays = StudentAttendance::where('registration_id', $registration->id)
            ->where('present', 1)
            ->count();
        $absentDays = $totalDays - $presentDays;
        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;
        
        return view('backend.parent.portal.child_attendance', compact(
            'student',
            'registration',
            'attendances',
            'totalDays',
            'presentDays',
            'absentDays',
            'attendancePercentage'
        ));
    }
    
    /**
     * Display child grades
     *
     * @param int $id Student ID
     * @return \Illuminate\Http\Response
     */
    public function childGrades($id)
    {
        // Get the authenticated parent
        $user = Auth::user();
        
        // Get the child
        $student = Student::findOrFail($id);
        
        // Check if this is the parent's child
        if ($student->guardian_phone_no != $user->phone_no && 
            $student->father_phone_no != $user->phone_no && 
            $student->mother_phone_no != $user->phone_no) {
            return redirect()->route('parent.portal.dashboard')->with('error', 'You do not have permission to view this student!');
        }
        
        // Get the current registration info
        $registration = Registration::where('student_id', $student->id)
            ->where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->first();
            
        if (!$registration) {
            return redirect()->route('parent.portal.dashboard')->with('error', 'No active registration found for this student!');
        }
        
        // Get all exams
        $exams = Exam::where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->get();
            
        // Get results for the student
        $results = Result::where('registration_id', $registration->id)
            ->orderBy('id', 'desc')
            ->get();
            
        // Get marks for the student
        $marks = Mark::where('registration_id', $registration->id)
            ->orderBy('id', 'desc')
            ->get();
        
        return view('backend.parent.portal.child_grades', compact(
            'student',
            'registration',
            'exams',
            'results',
            'marks'
        ));
    }
    
    /**
     * Display parent profile
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        // Get the authenticated parent
        $user = Auth::user();
        
        // Get children of this parent
        $children = Student::where('guardian_phone_no', $user->phone_no)
            ->orWhere('father_phone_no', $user->phone_no)
            ->orWhere('mother_phone_no', $user->phone_no)
            ->get();
        
        return view('backend.parent.portal.profile', compact(
            'user',
            'children'
        ));
    }
    
    /**
     * Update parent profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();
        
        // Validate the request
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'phone_no' => 'required|string|max:15',
        ]);
        
        // Update user information
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_no = $request->phone_no;
        $user->save();
        
        return redirect()->route('parent.portal.profile')->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Change parent password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();
        
        if ($request->isMethod('post')) {
            // Validate the request
            $this->validate($request, [
                'current_password' => 'required|min:6|max:50',
                'password' => 'required|confirmed|min:6|max:50',
            ]);
            
            // Check if current password is correct
            if (!password_verify($request->current_password, $user->password)) {
                return redirect()->back()->with('error', 'Current password is incorrect!');
            }
            
            // Update password
            $user->password = bcrypt($request->password);
            $user->save();
            
            return redirect()->route('parent.portal.profile')->with('success', 'Password changed successfully!');
        }
        
        return view('backend.parent.portal.change_password');
    }
}
