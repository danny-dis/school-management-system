<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Course;
use App\Assignment;
use App\AssignmentSubmission;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

/**
 * AssignmentController
 * 
 * This controller handles the assignment functionality for the online learning module.
 */
class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments for a course.
     *
     * @param  int  $courseId
     * @return \Illuminate\Http\Response
     */
    public function index($courseId)
    {
        $course = Course::with('assignments')->findOrFail($courseId);
        
        return view('backend.online_learning.assignments.index', compact('course'));
    }

    /**
     * Show the form for creating a new assignment.
     *
     * @param  int  $courseId
     * @return \Illuminate\Http\Response
     */
    public function create($courseId)
    {
        $course = Course::findOrFail($courseId);
        
        return view('backend.online_learning.assignments.create', compact('course'));
    }

    /**
     * Store a newly created assignment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        
        $this->validate($request, [
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'total_marks' => 'required|numeric|min:0',
            'attachment' => 'nullable|file|max:10240',
            'instructions' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        // Handle attachment upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('public/assignments');
            $attachmentPath = basename($attachmentPath);
        }

        // Create assignment
        $assignment = Assignment::create([
            'title' => $request->title,
            'description' => $request->description,
            'course_id' => $courseId,
            'due_date' => $request->due_date,
            'total_marks' => $request->total_marks,
            'attachment' => $attachmentPath,
            'instructions' => $request->instructions,
            'status' => $request->status
        ]);

        return redirect()->route('online_learning.assignments.index', $courseId)
            ->with('success', 'Assignment created successfully!');
    }

    /**
     * Display the specified assignment.
     *
     * @param  int  $courseId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($courseId, $id)
    {
        $course = Course::findOrFail($courseId);
        $assignment = Assignment::with('submissions.student.student')->findOrFail($id);
        
        return view('backend.online_learning.assignments.show', compact('course', 'assignment'));
    }

    /**
     * Show the form for editing the specified assignment.
     *
     * @param  int  $courseId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($courseId, $id)
    {
        $course = Course::findOrFail($courseId);
        $assignment = Assignment::findOrFail($id);
        
        return view('backend.online_learning.assignments.edit', compact('course', 'assignment'));
    }

    /**
     * Update the specified assignment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $courseId, $id)
    {
        $course = Course::findOrFail($courseId);
        $assignment = Assignment::findOrFail($id);
        
        $this->validate($request, [
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'total_marks' => 'required|numeric|min:0',
            'attachment' => 'nullable|file|max:10240',
            'instructions' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        // Handle attachment upload
        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($assignment->attachment) {
                Storage::delete('public/assignments/'.$assignment->attachment);
            }
            
            $attachmentPath = $request->file('attachment')->store('public/assignments');
            $assignment->attachment = basename($attachmentPath);
        }

        // Update assignment
        $assignment->title = $request->title;
        $assignment->description = $request->description;
        $assignment->due_date = $request->due_date;
        $assignment->total_marks = $request->total_marks;
        $assignment->instructions = $request->instructions;
        $assignment->status = $request->status;
        $assignment->save();

        return redirect()->route('online_learning.assignments.index', $courseId)
            ->with('success', 'Assignment updated successfully!');
    }

    /**
     * Remove the specified assignment from storage.
     *
     * @param  int  $courseId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($courseId, $id)
    {
        $assignment = Assignment::findOrFail($id);
        
        // Delete attachment if exists
        if ($assignment->attachment) {
            Storage::delete('public/assignments/'.$assignment->attachment);
        }
        
        $assignment->delete();
        
        return redirect()->route('online_learning.assignments.index', $courseId)
            ->with('success', 'Assignment deleted successfully!');
    }

    /**
     * Grade an assignment submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @param  int  $assignmentId
     * @param  int  $submissionId
     * @return \Illuminate\Http\Response
     */
    public function gradeSubmission(Request $request, $courseId, $assignmentId, $submissionId)
    {
        $submission = AssignmentSubmission::findOrFail($submissionId);
        $assignment = Assignment::findOrFail($assignmentId);
        
        $this->validate($request, [
            'marks' => 'required|numeric|min:0|max:'.$assignment->total_marks,
            'feedback' => 'nullable|string'
        ]);

        // Update submission
        $submission->marks = $request->marks;
        $submission->feedback = $request->feedback;
        $submission->graded_at = now();
        $submission->graded_by = Auth::id();
        $submission->save();

        return redirect()->route('online_learning.assignments.show', [$courseId, $assignmentId])
            ->with('success', 'Submission graded successfully!');
    }

    /**
     * View a submission.
     *
     * @param  int  $courseId
     * @param  int  $assignmentId
     * @param  int  $submissionId
     * @return \Illuminate\Http\Response
     */
    public function viewSubmission($courseId, $assignmentId, $submissionId)
    {
        $course = Course::findOrFail($courseId);
        $assignment = Assignment::findOrFail($assignmentId);
        $submission = AssignmentSubmission::with('student.student')->findOrFail($submissionId);
        
        return view('backend.online_learning.assignments.view_submission', compact('course', 'assignment', 'submission'));
    }
}
