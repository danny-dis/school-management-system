<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Course;
use App\Lesson;
use App\Assignment;
use App\IClass;
use App\Employee;
use App\Http\Helpers\AppHelper;

class OnlineLearningTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test course creation.
     *
     * @return void
     */
    public function testCourseCreation()
    {
        // Create a user with appropriate permissions
        $user = factory(User::class)->create();
        $this->actingAs($user);
        
        // Create a class and teacher for the course
        $class = factory(IClass::class)->create();
        $teacher = factory(Employee::class)->create(['role_id' => AppHelper::EMP_TEACHER]);
        
        // Test course creation
        $response = $this->post(route('online_learning.courses.store'), [
            'name' => 'Test Course',
            'code' => 'TEST101',
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'description' => 'This is a test course',
            'status' => AppHelper::ACTIVE
        ]);
        
        $response->assertRedirect(route('online_learning.courses'));
        $this->assertDatabaseHas('courses', [
            'name' => 'Test Course',
            'code' => 'TEST101'
        ]);
    }
    
    /**
     * Test lesson creation.
     *
     * @return void
     */
    public function testLessonCreation()
    {
        // Create a user with appropriate permissions
        $user = factory(User::class)->create();
        $this->actingAs($user);
        
        // Create a class and teacher for the course
        $class = factory(IClass::class)->create();
        $teacher = factory(Employee::class)->create(['role_id' => AppHelper::EMP_TEACHER]);
        
        // Create a course
        $course = Course::create([
            'name' => 'Test Course',
            'code' => 'TEST101',
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'description' => 'This is a test course',
            'status' => AppHelper::ACTIVE
        ]);
        
        // Test lesson creation
        $response = $this->post(route('online_learning.lessons.store', $course->id), [
            'title' => 'Test Lesson',
            'description' => 'This is a test lesson',
            'content' => 'Lesson content goes here',
            'order' => 1,
            'status' => AppHelper::ACTIVE
        ]);
        
        $response->assertRedirect(route('online_learning.lessons.index', $course->id));
        $this->assertDatabaseHas('lessons', [
            'title' => 'Test Lesson',
            'course_id' => $course->id
        ]);
    }
    
    /**
     * Test assignment creation.
     *
     * @return void
     */
    public function testAssignmentCreation()
    {
        // Create a user with appropriate permissions
        $user = factory(User::class)->create();
        $this->actingAs($user);
        
        // Create a class and teacher for the course
        $class = factory(IClass::class)->create();
        $teacher = factory(Employee::class)->create(['role_id' => AppHelper::EMP_TEACHER]);
        
        // Create a course
        $course = Course::create([
            'name' => 'Test Course',
            'code' => 'TEST101',
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'description' => 'This is a test course',
            'status' => AppHelper::ACTIVE
        ]);
        
        // Test assignment creation
        $response = $this->post(route('online_learning.assignments.store', $course->id), [
            'title' => 'Test Assignment',
            'description' => 'This is a test assignment',
            'due_date' => now()->addDays(7),
            'total_marks' => 100,
            'status' => AppHelper::ACTIVE
        ]);
        
        $response->assertRedirect(route('online_learning.assignments.index', $course->id));
        $this->assertDatabaseHas('assignments', [
            'title' => 'Test Assignment',
            'course_id' => $course->id
        ]);
    }
    
    /**
     * Test student enrollment.
     *
     * @return void
     */
    public function testStudentEnrollment()
    {
        // Create a user with appropriate permissions
        $user = factory(User::class)->create();
        $this->actingAs($user);
        
        // Create a class and teacher for the course
        $class = factory(IClass::class)->create();
        $teacher = factory(Employee::class)->create(['role_id' => AppHelper::EMP_TEACHER]);
        
        // Create a course
        $course = Course::create([
            'name' => 'Test Course',
            'code' => 'TEST101',
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'description' => 'This is a test course',
            'status' => AppHelper::ACTIVE
        ]);
        
        // Create a student
        $student = factory(\App\Registration::class)->create(['class_id' => $class->id]);
        
        // Test student enrollment
        $response = $this->post(route('online_learning.courses.enroll', $course->id), [
            'student_ids' => [$student->id]
        ]);
        
        $response->assertRedirect(route('online_learning.courses.show', $course->id));
        $this->assertDatabaseHas('course_student', [
            'course_id' => $course->id,
            'registration_id' => $student->id
        ]);
    }
}
