<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Course;
use App\Lesson;
use App\Assignment;
use App\AssignmentSubmission;
use App\LessonResource;
use App\LessonProgress;
use App\IClass;
use App\Employee;
use App\Registration;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * OnlineLearningController
 * 
 * This controller handles the online learning module functionality.
 */
class OnlineLearningController extends Controller
{
    /**
     * Display a listing of courses.
     *
     * @return \Illuminate\Http\Response
     */
    public function courses()
    {
        $courses = Course::with('class', 'teacher')->orderBy('id', 'desc')->paginate(10);
        return view('backend.online_learning.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     *
     * @return \Illuminate\Http\Response
     */
    public function createCourse()
    {
        $classes = IClass::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        $teachers = Employee::where('role_id', AppHelper::EMP_TEACHER)->pluck('name', 'id');
        
        return view('backend.online_learning.courses.create', compact('classes', 'teachers'));
    }

    /**
     * Store a newly created course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCourse(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:courses',
            'class_id' => 'required|integer|exists:i_classes,id',
            'teacher_id' => 'required|integer|exists:employees,id',
            'description' => 'nullable|string',
            'syllabus' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|integer'
        ]);

        // Handle cover image upload
        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('public/courses');
            $coverImagePath = basename($coverImagePath);
        }

        // Create course
        $course = Course::create([
            'name' => $request->name,
            'code' => $request->code,
            'class_id' => $request->class_id,
            'teacher_id' => $request->teacher_id,
            'description' => $request->description,
            'syllabus' => $request->syllabus,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'cover_image' => $coverImagePath,
            'status' => $request->status
        ]);

        return redirect()->route('online_learning.courses')->with('success', 'Course created successfully!');
    }

    /**
     * Display the specified course.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showCourse($id)
    {
        $course = Course::with('class', 'teacher', 'lessons', 'assignments')->findOrFail($id);
        $students = $course->students()->with('student')->get();
        
        return view('backend.online_learning.courses.show', compact('course', 'students'));
    }

    /**
     * Show the form for editing the specified course.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editCourse($id)
    {
        $course = Course::findOrFail($id);
        $classes = IClass::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        $teachers = Employee::where('role_id', AppHelper::EMP_TEACHER)->pluck('name', 'id');
        
        return view('backend.online_learning.courses.edit', compact('course', 'classes', 'teachers'));
    }

    /**
     * Update the specified course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:courses,code,'.$id,
            'class_id' => 'required|integer|exists:i_classes,id',
            'teacher_id' => 'required|integer|exists:employees,id',
            'description' => 'nullable|string',
            'syllabus' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|integer'
        ]);

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($course->cover_image) {
                Storage::delete('public/courses/'.$course->cover_image);
            }
            
            $coverImagePath = $request->file('cover_image')->store('public/courses');
            $course->cover_image = basename($coverImagePath);
        }

        // Update course
        $course->name = $request->name;
        $course->code = $request->code;
        $course->class_id = $request->class_id;
        $course->teacher_id = $request->teacher_id;
        $course->description = $request->description;
        $course->syllabus = $request->syllabus;
        $course->start_date = $request->start_date;
        $course->end_date = $request->end_date;
        $course->status = $request->status;
        $course->save();

        return redirect()->route('online_learning.courses')->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified course from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyCourse($id)
    {
        $course = Course::findOrFail($id);
        
        // Delete cover image if exists
        if ($course->cover_image) {
            Storage::delete('public/courses/'.$course->cover_image);
        }
        
        $course->delete();
        
        return redirect()->route('online_learning.courses')->with('success', 'Course deleted successfully!');
    }

    /**
     * Manage students enrolled in a course.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function manageStudents($id)
    {
        $course = Course::with('students')->findOrFail($id);
        $class = $course->class;
        
        // Get all students in the class
        $classStudents = Registration::where('class_id', $class->id)
            ->where('status', AppHelper::ACTIVE)
            ->with('student')
            ->get();
            
        // Get IDs of enrolled students
        $enrolledStudentIds = $course->students->pluck('id')->toArray();
        
        return view('backend.online_learning.courses.manage_students', compact('course', 'classStudents', 'enrolledStudentIds'));
    }

    /**
     * Enroll students in a course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function enrollStudents(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        $this->validate($request, [
            'student_ids' => 'required|array',
            'student_ids.*' => 'integer|exists:registrations,id'
        ]);
        
        // Sync students
        $course->students()->sync($request->student_ids);
        
        return redirect()->route('online_learning.courses.show', $id)->with('success', 'Students enrolled successfully!');
    }
}
