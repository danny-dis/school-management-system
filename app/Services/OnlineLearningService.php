<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class OnlineLearningService
{
    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * OnlineLearningService constructor.
     *
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create a course
     *
     * @param array $data
     * @param int $createdBy
     * @return Course|null
     */
    public function createCourse(array $data, $createdBy)
    {
        try {
            DB::beginTransaction();
            
            $course = Course::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'class_id' => $data['class_id'] ?? null,
                'section_id' => $data['section_id'] ?? null,
                'subject_id' => $data['subject_id'] ?? null,
                'teacher_id' => $data['teacher_id'] ?? null,
                'thumbnail' => $data['thumbnail'] ?? null,
                'status' => $data['status'] ?? Course::STATUS_DRAFT,
                'created_by' => $createdBy
            ]);
            
            // Notify students if course is published
            if ($course->status == Course::STATUS_PUBLISHED) {
                $this->notifyStudentsAboutCourse($course);
            }
            
            DB::commit();
            return $course;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating course: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a course
     *
     * @param int $id
     * @param array $data
     * @return Course|null
     */
    public function updateCourse($id, array $data)
    {
        try {
            DB::beginTransaction();
            
            $course = Course::find($id);
            
            if (!$course) {
                return null;
            }
            
            $oldStatus = $course->status;
            
            $course->update($data);
            
            // Notify students if course is newly published
            if ($oldStatus != Course::STATUS_PUBLISHED && $course->status == Course::STATUS_PUBLISHED) {
                $this->notifyStudentsAboutCourse($course);
            }
            
            DB::commit();
            return $course;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating course: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a course
     *
     * @param int $id
     * @return bool
     */
    public function deleteCourse($id)
    {
        try {
            DB::beginTransaction();
            
            $course = Course::find($id);
            
            if (!$course) {
                return false;
            }
            
            // Delete lessons
            $course->lessons()->delete();
            
            // Delete assignments
            $course->assignments()->delete();
            
            // Delete quizzes
            $course->quizzes()->delete();
            
            // Delete course
            $course->delete();
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting course: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Create a lesson
     *
     * @param array $data
     * @param int $createdBy
     * @return Lesson|null
     */
    public function createLesson(array $data, $createdBy)
    {
        try {
            DB::beginTransaction();
            
            $lesson = Lesson::create([
                'course_id' => $data['course_id'],
                'title' => $data['title'],
                'content' => $data['content'],
                'video_url' => $data['video_url'] ?? null,
                'attachment' => $data['attachment'] ?? null,
                'order' => $data['order'] ?? 0,
                'status' => $data['status'] ?? Lesson::STATUS_DRAFT,
                'created_by' => $createdBy
            ]);
            
            // Notify students if lesson is published
            if ($lesson->status == Lesson::STATUS_PUBLISHED) {
                $this->notifyStudentsAboutLesson($lesson);
            }
            
            DB::commit();
            return $lesson;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating lesson: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a lesson
     *
     * @param int $id
     * @param array $data
     * @return Lesson|null
     */
    public function updateLesson($id, array $data)
    {
        try {
            DB::beginTransaction();
            
            $lesson = Lesson::find($id);
            
            if (!$lesson) {
                return null;
            }
            
            $oldStatus = $lesson->status;
            
            $lesson->update($data);
            
            // Notify students if lesson is newly published
            if ($oldStatus != Lesson::STATUS_PUBLISHED && $lesson->status == Lesson::STATUS_PUBLISHED) {
                $this->notifyStudentsAboutLesson($lesson);
            }
            
            DB::commit();
            return $lesson;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating lesson: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a lesson
     *
     * @param int $id
     * @return bool
     */
    public function deleteLesson($id)
    {
        try {
            $lesson = Lesson::find($id);
            
            if (!$lesson) {
                return false;
            }
            
            $lesson->delete();
            return true;
        } catch (Exception $e) {
            Log::error('Error deleting lesson: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Create an assignment
     *
     * @param array $data
     * @param int $createdBy
     * @return Assignment|null
     */
    public function createAssignment(array $data, $createdBy)
    {
        try {
            DB::beginTransaction();
            
            $assignment = Assignment::create([
                'course_id' => $data['course_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'attachment' => $data['attachment'] ?? null,
                'due_date' => $data['due_date'],
                'total_marks' => $data['total_marks'],
                'status' => $data['status'] ?? Assignment::STATUS_DRAFT,
                'created_by' => $createdBy
            ]);
            
            // Notify students if assignment is published
            if ($assignment->status == Assignment::STATUS_PUBLISHED) {
                $this->notifyStudentsAboutAssignment($assignment);
            }
            
            DB::commit();
            return $assignment;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating assignment: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update an assignment
     *
     * @param int $id
     * @param array $data
     * @return Assignment|null
     */
    public function updateAssignment($id, array $data)
    {
        try {
            DB::beginTransaction();
            
            $assignment = Assignment::find($id);
            
            if (!$assignment) {
                return null;
            }
            
            $oldStatus = $assignment->status;
            
            $assignment->update($data);
            
            // Notify students if assignment is newly published
            if ($oldStatus != Assignment::STATUS_PUBLISHED && $assignment->status == Assignment::STATUS_PUBLISHED) {
                $this->notifyStudentsAboutAssignment($assignment);
            }
            
            DB::commit();
            return $assignment;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating assignment: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete an assignment
     *
     * @param int $id
     * @return bool
     */
    public function deleteAssignment($id)
    {
        try {
            DB::beginTransaction();
            
            $assignment = Assignment::find($id);
            
            if (!$assignment) {
                return false;
            }
            
            // Delete submissions
            $assignment->submissions()->delete();
            
            // Delete assignment
            $assignment->delete();
            
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting assignment: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Submit an assignment
     *
     * @param array $data
     * @param int $studentId
     * @return AssignmentSubmission|null
     */
    public function submitAssignment(array $data, $studentId)
    {
        try {
            DB::beginTransaction();
            
            $submission = AssignmentSubmission::create([
                'assignment_id' => $data['assignment_id'],
                'student_id' => $studentId,
                'content' => $data['content'],
                'attachment' => $data['attachment'] ?? null,
                'status' => AssignmentSubmission::STATUS_SUBMITTED
            ]);
            
            // Notify teacher about submission
            $assignment = Assignment::find($data['assignment_id']);
            if ($assignment && $assignment->course && $assignment->course->teacher_id) {
                $this->notificationService->createNotification(
                    $assignment->course->teacher_id,
                    'New Assignment Submission',
                    'A student has submitted the assignment: ' . $assignment->title,
                    route('online_learning.assignments.submissions', $assignment->id),
                    'info'
                );
            }
            
            DB::commit();
            return $submission;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error submitting assignment: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Grade an assignment submission
     *
     * @param int $submissionId
     * @param float $marks
     * @param string|null $feedback
     * @param int $gradedBy
     * @return AssignmentSubmission|null
     */
    public function gradeAssignment($submissionId, $marks, $feedback, $gradedBy)
    {
        try {
            DB::beginTransaction();
            
            $submission = AssignmentSubmission::find($submissionId);
            
            if (!$submission) {
                return null;
            }
            
            $submission->marks = $marks;
            $submission->feedback = $feedback;
            $submission->graded_by = $gradedBy;
            $submission->graded_at = now();
            $submission->status = AssignmentSubmission::STATUS_GRADED;
            $submission->save();
            
            // Notify student about grading
            $this->notificationService->createNotification(
                $submission->student_id,
                'Assignment Graded',
                'Your assignment has been graded: ' . $submission->assignment->title,
                route('online_learning.assignments.view_submission', $submission->id),
                'info'
            );
            
            DB::commit();
            return $submission;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error grading assignment: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Get courses for a class
     *
     * @param int $classId
     * @param int|null $sectionId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCoursesForClass($classId, $sectionId = null)
    {
        $query = Course::where('class_id', $classId)
            ->where('status', Course::STATUS_PUBLISHED);
        
        if ($sectionId) {
            $query->where(function ($q) use ($sectionId) {
                $q->where('section_id', $sectionId)
                    ->orWhereNull('section_id');
            });
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get courses for a teacher
     *
     * @param int $teacherId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCoursesForTeacher($teacherId)
    {
        return Course::where('teacher_id', $teacherId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get lessons for a course
     *
     * @param int $courseId
     * @param bool $publishedOnly
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLessonsForCourse($courseId, $publishedOnly = false)
    {
        $query = Lesson::where('course_id', $courseId);
        
        if ($publishedOnly) {
            $query->where('status', Lesson::STATUS_PUBLISHED);
        }
        
        return $query->orderBy('order', 'asc')->get();
    }

    /**
     * Get assignments for a course
     *
     * @param int $courseId
     * @param bool $publishedOnly
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAssignmentsForCourse($courseId, $publishedOnly = false)
    {
        $query = Assignment::where('course_id', $courseId);
        
        if ($publishedOnly) {
            $query->where('status', Assignment::STATUS_PUBLISHED);
        }
        
        return $query->orderBy('due_date', 'asc')->get();
    }

    /**
     * Get submissions for an assignment
     *
     * @param int $assignmentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSubmissionsForAssignment($assignmentId)
    {
        return AssignmentSubmission::with('student')
            ->where('assignment_id', $assignmentId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get submission for a student
     *
     * @param int $assignmentId
     * @param int $studentId
     * @return AssignmentSubmission|null
     */
    public function getSubmissionForStudent($assignmentId, $studentId)
    {
        return AssignmentSubmission::where('assignment_id', $assignmentId)
            ->where('student_id', $studentId)
            ->first();
    }

    /**
     * Notify students about a course
     *
     * @param Course $course
     * @return void
     */
    protected function notifyStudentsAboutCourse(Course $course)
    {
        // Get students in the class/section
        $query = \App\Models\Student::whereHas('registration', function ($q) use ($course) {
            $q->where('class_id', $course->class_id);
            
            if ($course->section_id) {
                $q->where('section_id', $course->section_id);
            }
        });
        
        $students = $query->get();
        
        foreach ($students as $student) {
            $this->notificationService->createNotification(
                $student->user_id,
                'New Course Available',
                'A new course has been published: ' . $course->title,
                route('online_learning.courses.show', $course->id),
                'info'
            );
        }
    }

    /**
     * Notify students about a lesson
     *
     * @param Lesson $lesson
     * @return void
     */
    protected function notifyStudentsAboutLesson(Lesson $lesson)
    {
        if (!$lesson->course) {
            return;
        }
        
        // Get students in the class/section
        $query = \App\Models\Student::whereHas('registration', function ($q) use ($lesson) {
            $q->where('class_id', $lesson->course->class_id);
            
            if ($lesson->course->section_id) {
                $q->where('section_id', $lesson->course->section_id);
            }
        });
        
        $students = $query->get();
        
        foreach ($students as $student) {
            $this->notificationService->createNotification(
                $student->user_id,
                'New Lesson Available',
                'A new lesson has been published in the course: ' . $lesson->course->title,
                route('online_learning.lessons.show', $lesson->id),
                'info'
            );
        }
    }

    /**
     * Notify students about an assignment
     *
     * @param Assignment $assignment
     * @return void
     */
    protected function notifyStudentsAboutAssignment(Assignment $assignment)
    {
        if (!$assignment->course) {
            return;
        }
        
        // Get students in the class/section
        $query = \App\Models\Student::whereHas('registration', function ($q) use ($assignment) {
            $q->where('class_id', $assignment->course->class_id);
            
            if ($assignment->course->section_id) {
                $q->where('section_id', $assignment->course->section_id);
            }
        });
        
        $students = $query->get();
        
        foreach ($students as $student) {
            $this->notificationService->createNotification(
                $student->user_id,
                'New Assignment',
                'A new assignment has been published in the course: ' . $assignment->course->title,
                route('online_learning.assignments.show', $assignment->id),
                'info'
            );
        }
    }
}
