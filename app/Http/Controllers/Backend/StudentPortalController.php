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

class StudentPortalController extends Controller
{
    /**
     * Display student dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        // Get the authenticated student
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'No student profile found for this account!');
        }
        
        // Get the current registration info
        $registration = Registration::where('student_id', $student->id)
            ->where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->first();
            
        if (!$registration) {
            return redirect()->route('login')->with('error', 'No active registration found!');
        }
        
        // Get recent attendance
        $recentAttendance = StudentAttendance::where('registration_id', $registration->id)
            ->orderBy('attendance_date', 'desc')
            ->take(10)
            ->get();
            
        // Get recent exams
        $recentExams = Exam::where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();
            
        // Get subjects for the student
        $subjects = Subject::whereHas('students', function ($query) use ($registration) {
            $query->where('registration_id', $registration->id);
        })->get();
        
        return view('backend.student.portal.dashboard', compact(
            'student',
            'registration',
            'recentAttendance',
            'recentExams',
            'subjects'
        ));
    }
    
    /**
     * Display student attendance
     *
     * @return \Illuminate\Http\Response
     */
    public function attendance()
    {
        // Get the authenticated student
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'No student profile found for this account!');
        }
        
        // Get the current registration info
        $registration = Registration::where('student_id', $student->id)
            ->where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->first();
            
        if (!$registration) {
            return redirect()->route('login')->with('error', 'No active registration found!');
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
        
        return view('backend.student.portal.attendance', compact(
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
     * Display student grades and results
     *
     * @return \Illuminate\Http\Response
     */
    public function grades()
    {
        // Get the authenticated student
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'No student profile found for this account!');
        }
        
        // Get the current registration info
        $registration = Registration::where('student_id', $student->id)
            ->where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->first();
            
        if (!$registration) {
            return redirect()->route('login')->with('error', 'No active registration found!');
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
        
        return view('backend.student.portal.grades', compact(
            'student',
            'registration',
            'exams',
            'results',
            'marks'
        ));
    }
    
    /**
     * Display student subjects and course materials
     *
     * @return \Illuminate\Http\Response
     */
    public function subjects()
    {
        // Get the authenticated student
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'No student profile found for this account!');
        }
        
        // Get the current registration info
        $registration = Registration::where('student_id', $student->id)
            ->where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->first();
            
        if (!$registration) {
            return redirect()->route('login')->with('error', 'No active registration found!');
        }
        
        // Get subjects for the student
        $subjects = Subject::whereHas('students', function ($query) use ($registration) {
            $query->where('registration_id', $registration->id);
        })->get();
        
        return view('backend.student.portal.subjects', compact(
            'student',
            'registration',
            'subjects'
        ));
    }
    
    /**
     * Display student profile
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        // Get the authenticated student
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'No student profile found for this account!');
        }
        
        // Get the current registration info
        $registration = Registration::where('student_id', $student->id)
            ->where('status', AppHelper::ACTIVE)
            ->orderBy('id', 'desc')
            ->first();
            
        if (!$registration) {
            return redirect()->route('login')->with('error', 'No active registration found!');
        }
        
        return view('backend.student.portal.profile', compact(
            'student',
            'registration',
            'user'
        ));
    }
    
    /**
     * Update student profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        // Get the authenticated student
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'No student profile found for this account!');
        }
        
        // Validate the request
        $this->validate($request, [
            'email' => 'required|email|max:255',
            'phone_no' => 'required|string|max:15',
            'present_address' => 'required|string|max:500',
            'permanent_address' => 'required|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Update student information
        $student->email = $request->email;
        $student->phone_no = $request->phone_no;
        $student->present_address = $request->present_address;
        $student->permanent_address = $request->permanent_address;
        
        // Handle photo upload
        if ($request->hasFile('photo')) {
            $imgStorePath = "public/student/";
            $storagepath = $request->file('photo')->store($imgStorePath);
            $fileName = basename($storagepath);
            $student->photo = $fileName;
        }
        
        $student->save();
        
        // Update user email
        $user->email = $request->email;
        $user->phone_no = $request->phone_no;
        $user->save();
        
        return redirect()->route('student.portal.profile')->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Change student password
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
            
            return redirect()->route('student.portal.profile')->with('success', 'Password changed successfully!');
        }
        
        return view('backend.student.portal.change_password');
    }
}
