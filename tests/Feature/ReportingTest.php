<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\UserRole;
use App\Registration;
use App\Student;
use App\IClass;
use App\Section;
use App\AcademicYear;
use App\Http\Helpers\AppHelper;

class ReportingTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test reporting dashboard.
     *
     * @return void
     */
    public function testReportingDashboard()
    {
        // Create a user with admin role
        $role = UserRole::create([
            'name' => 'Admin',
            'deletable' => false
        ]);
        
        $user = User::create([
            'name' => 'Test Admin',
            'username' => 'testadmin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'status' => 1
        ]);
        
        // Test dashboard access
        $response = $this->actingAs($user)
            ->get(route('reporting.dashboard'));
        
        $response->assertStatus(200);
    }
    
    /**
     * Test student reports.
     *
     * @return void
     */
    public function testStudentReports()
    {
        // Create a user with admin role
        $role = UserRole::create([
            'name' => 'Admin',
            'deletable' => false
        ]);
        
        $user = User::create([
            'name' => 'Test Admin',
            'username' => 'testadmin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'status' => 1
        ]);
        
        // Create necessary data
        $class = factory(IClass::class)->create();
        $section = factory(Section::class)->create(['class_id' => $class->id]);
        $academicYear = factory(AcademicYear::class)->create(['status' => '1']);
        
        // Create students
        $student1 = Student::create([
            'name' => 'Student 1',
            'dob' => '2000-01-01',
            'gender' => 'Male',
            'religion' => 'Christianity',
            'email' => 'student1@example.com'
        ]);
        
        $student2 = Student::create([
            'name' => 'Student 2',
            'dob' => '2000-02-02',
            'gender' => 'Female',
            'religion' => 'Islam',
            'email' => 'student2@example.com'
        ]);
        
        // Create registrations
        $registration1 = Registration::create([
            'student_id' => $student1->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'roll_no' => '101',
            'academic_year_id' => $academicYear->id,
            'status' => AppHelper::ACTIVE
        ]);
        
        $registration2 = Registration::create([
            'student_id' => $student2->id,
            'class_id' => $class->id,
            'section_id' => $section->id,
            'roll_no' => '102',
            'academic_year_id' => $academicYear->id,
            'status' => AppHelper::ACTIVE
        ]);
        
        // Test student reports page
        $response = $this->actingAs($user)
            ->get(route('reporting.students'));
        
        $response->assertStatus(200);
        
        // Test generate student list
        $response = $this->actingAs($user)
            ->post(route('reporting.students.list'), [
                'academic_year_id' => $academicYear->id,
                'class_id' => $class->id,
                'section_id' => $section->id,
                'report_type' => 'html'
            ]);
        
        $response->assertStatus(200)
            ->assertSee('Student 1')
            ->assertSee('Student 2');
        
        // Test generate student list with gender filter
        $response = $this->actingAs($user)
            ->post(route('reporting.students.list'), [
                'academic_year_id' => $academicYear->id,
                'class_id' => $class->id,
                'section_id' => $section->id,
                'gender' => 'Male',
                'report_type' => 'html'
            ]);
        
        $response->assertStatus(200)
            ->assertSee('Student 1')
            ->assertDontSee('Student 2');
    }
    
    /**
     * Test attendance reports.
     *
     * @return void
     */
    public function testAttendanceReports()
    {
        // Create a user with admin role
        $role = UserRole::create([
            'name' => 'Admin',
            'deletable' => false
        ]);
        
        $user = User::create([
            'name' => 'Test Admin',
            'username' => 'testadmin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'status' => 1
        ]);
        
        // Create necessary data
        $class = factory(IClass::class)->create();
        $section = factory(Section::class)->create(['class_id' => $class->id]);
        
        // Test attendance reports page
        $response = $this->actingAs($user)
            ->get(route('reporting.attendance'));
        
        $response->assertStatus(200);
    }
    
    /**
     * Test exam reports.
     *
     * @return void
     */
    public function testExamReports()
    {
        // Create a user with admin role
        $role = UserRole::create([
            'name' => 'Admin',
            'deletable' => false
        ]);
        
        $user = User::create([
            'name' => 'Test Admin',
            'username' => 'testadmin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'status' => 1
        ]);
        
        // Test exam reports page
        $response = $this->actingAs($user)
            ->get(route('reporting.exams'));
        
        $response->assertStatus(200);
    }
    
    /**
     * Test financial reports.
     *
     * @return void
     */
    public function testFinancialReports()
    {
        // Create a user with admin role
        $role = UserRole::create([
            'name' => 'Admin',
            'deletable' => false
        ]);
        
        $user = User::create([
            'name' => 'Test Admin',
            'username' => 'testadmin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'status' => 1
        ]);
        
        // Test financial reports page
        $response = $this->actingAs($user)
            ->get(route('reporting.financial'));
        
        $response->assertStatus(200);
    }
}
