<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\UserRole;
use App\Registration;
use App\Student;
use App\Employee;
use App\Http\Helpers\AppHelper;

class ApiTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test user login.
     *
     * @return void
     */
    public function testUserLogin()
    {
        // Create a user role
        $role = UserRole::create([
            'name' => 'Student',
            'deletable' => false
        ]);
        
        // Create a user
        $user = User::create([
            'name' => 'Test Student',
            'username' => 'teststudent',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'status' => 1
        ]);
        
        // Test login
        $response = $this->postJson('/api/login', [
            'username' => 'teststudent',
            'password' => 'password',
            'device_name' => 'test_device'
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'username',
                        'email',
                        'role'
                    ],
                    'token'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Login successful'
            ]);
    }
    
    /**
     * Test invalid login.
     *
     * @return void
     */
    public function testInvalidLogin()
    {
        // Create a user role
        $role = UserRole::create([
            'name' => 'Student',
            'deletable' => false
        ]);
        
        // Create a user
        $user = User::create([
            'name' => 'Test Student',
            'username' => 'teststudent',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'status' => 1
        ]);
        
        // Test login with wrong password
        $response = $this->postJson('/api/login', [
            'username' => 'teststudent',
            'password' => 'wrongpassword',
            'device_name' => 'test_device'
        ]);
        
        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);
    }
    
    /**
     * Test student profile.
     *
     * @return void
     */
    public function testStudentProfile()
    {
        // Create a user role
        $role = UserRole::create([
            'name' => 'Student',
            'deletable' => false
        ]);
        
        // Create a user
        $user = User::create([
            'name' => 'Test Student',
            'username' => 'teststudent',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'status' => 1
        ]);
        
        // Create a student
        $student = Student::create([
            'user_id' => $user->id,
            'name' => 'Test Student',
            'dob' => '2000-01-01',
            'gender' => 'Male',
            'religion' => 'Christianity',
            'email' => 'test@example.com',
            'phone_no' => '1234567890',
            'father_name' => 'Father Name',
            'mother_name' => 'Mother Name'
        ]);
        
        // Create class, section, and registration
        $class = factory(\App\IClass::class)->create();
        $section = factory(\App\Section::class)->create(['class_id' => $class->id]);
        $academicYear = factory(\App\AcademicYear::class)->create(['status' => '1']);
        
        $registration = Registration::create([
            'student_id' => $user->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'roll_no' => '101',
            'academic_year_id' => $academicYear->id,
            'status' => AppHelper::ACTIVE
        ]);
        
        // Test profile
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/student/profile');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'student' => [
                        'id',
                        'name',
                        'roll_no',
                        'class',
                        'section',
                        'academic_year',
                        'dob',
                        'gender',
                        'religion',
                        'email',
                        'phone',
                        'father_name',
                        'mother_name'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'student' => [
                        'name' => 'Test Student',
                        'roll_no' => '101',
                        'gender' => 'Male',
                        'email' => 'test@example.com'
                    ]
                ]
            ]);
    }
    
    /**
     * Test teacher profile.
     *
     * @return void
     */
    public function testTeacherProfile()
    {
        // Create a user role
        $role = UserRole::create([
            'name' => 'Teacher',
            'deletable' => false
        ]);
        
        // Create a user
        $user = User::create([
            'name' => 'Test Teacher',
            'username' => 'testteacher',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'status' => 1
        ]);
        
        // Create a teacher
        $teacher = Employee::create([
            'id' => $user->id,
            'name' => 'Test Teacher',
            'designation' => 'Senior Teacher',
            'employee_id' => 'T101',
            'dob' => '1980-01-01',
            'gender' => 'Male',
            'religion' => 'Christianity',
            'email' => 'teacher@example.com',
            'phone_no' => '1234567890',
            'address' => 'Test Address',
            'joining_date' => '2020-01-01',
            'role_id' => $role->id
        ]);
        
        // Test profile
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/teacher/profile');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'teacher' => [
                        'id',
                        'name',
                        'designation',
                        'employee_id',
                        'role',
                        'dob',
                        'gender',
                        'religion',
                        'email',
                        'phone',
                        'address',
                        'joining_date'
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'teacher' => [
                        'name' => 'Test Teacher',
                        'designation' => 'Senior Teacher',
                        'employee_id' => 'T101',
                        'gender' => 'Male',
                        'email' => 'teacher@example.com'
                    ]
                ]
            ]);
    }
}
