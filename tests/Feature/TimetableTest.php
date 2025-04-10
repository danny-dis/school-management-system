<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\UserRole;
use App\Timetable;
use App\TimetableSlot;
use App\Room;
use App\IClass;
use App\Section;
use App\Subject;
use App\Employee;
use App\AcademicYear;
use App\Http\Helpers\AppHelper;

class TimetableTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test room creation.
     *
     * @return void
     */
    public function testRoomCreation()
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
        
        // Test room creation
        $response = $this->actingAs($user)
            ->post(route('timetable.rooms.store'), [
                'name' => 'Test Room',
                'room_no' => 'R101',
                'capacity' => 30,
                'type' => 'classroom',
                'description' => 'This is a test room',
                'status' => AppHelper::ACTIVE
            ]);
        
        $response->assertRedirect(route('timetable.rooms'));
        
        // Check if room was created
        $this->assertDatabaseHas('rooms', [
            'name' => 'Test Room',
            'room_no' => 'R101',
            'capacity' => 30,
            'type' => 'classroom'
        ]);
    }
    
    /**
     * Test timetable creation.
     *
     * @return void
     */
    public function testTimetableCreation()
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
        
        // Test timetable creation
        $response = $this->actingAs($user)
            ->post(route('timetable.store'), [
                'name' => 'Test Timetable',
                'class_id' => $class->id,
                'section_id' => $section->id,
                'academic_year_id' => $academicYear->id,
                'description' => 'This is a test timetable',
                'status' => AppHelper::ACTIVE
            ]);
        
        $timetable = Timetable::first();
        
        $response->assertRedirect(route('timetable.slots', $timetable->id));
        
        // Check if timetable was created
        $this->assertDatabaseHas('timetables', [
            'name' => 'Test Timetable',
            'class_id' => $class->id,
            'section_id' => $section->id,
            'academic_year_id' => $academicYear->id,
            'created_by' => $user->id
        ]);
    }
    
    /**
     * Test timetable slot creation.
     *
     * @return void
     */
    public function testTimetableSlotCreation()
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
        $subject = factory(Subject::class)->create(['class_id' => $class->id]);
        $teacher = factory(Employee::class)->create(['role_id' => AppHelper::EMP_TEACHER]);
        $room = Room::create([
            'name' => 'Test Room',
            'room_no' => 'R101',
            'capacity' => 30,
            'type' => 'classroom',
            'status' => AppHelper::ACTIVE
        ]);
        
        // Create timetable
        $timetable = Timetable::create([
            'name' => 'Test Timetable',
            'class_id' => $class->id,
            'section_id' => $section->id,
            'academic_year_id' => $academicYear->id,
            'description' => 'This is a test timetable',
            'status' => AppHelper::ACTIVE,
            'created_by' => $user->id
        ]);
        
        // Test slot creation
        $response = $this->actingAs($user)
            ->post(route('timetable.slots.store', $timetable->id), [
                'day' => 1, // Monday
                'start_time' => '09:00',
                'end_time' => '10:00',
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'room_id' => $room->id
            ]);
        
        $response->assertRedirect(route('timetable.slots', $timetable->id));
        
        // Check if slot was created
        $this->assertDatabaseHas('timetable_slots', [
            'timetable_id' => $timetable->id,
            'day' => 1,
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'room_id' => $room->id
        ]);
    }
    
    /**
     * Test timetable slot conflict detection.
     *
     * @return void
     */
    public function testTimetableSlotConflict()
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
        $subject = factory(Subject::class)->create(['class_id' => $class->id]);
        $teacher = factory(Employee::class)->create(['role_id' => AppHelper::EMP_TEACHER]);
        $room = Room::create([
            'name' => 'Test Room',
            'room_no' => 'R101',
            'capacity' => 30,
            'type' => 'classroom',
            'status' => AppHelper::ACTIVE
        ]);
        
        // Create timetable
        $timetable = Timetable::create([
            'name' => 'Test Timetable',
            'class_id' => $class->id,
            'section_id' => $section->id,
            'academic_year_id' => $academicYear->id,
            'description' => 'This is a test timetable',
            'status' => AppHelper::ACTIVE,
            'created_by' => $user->id
        ]);
        
        // Create a slot
        TimetableSlot::create([
            'timetable_id' => $timetable->id,
            'day' => 1, // Monday
            'start_time' => '09:00',
            'end_time' => '10:00',
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'room_id' => $room->id
        ]);
        
        // Test creating a conflicting slot
        $response = $this->actingAs($user)
            ->post(route('timetable.slots.store', $timetable->id), [
                'day' => 1, // Monday
                'start_time' => '09:30', // Overlaps with existing slot
                'end_time' => '10:30',
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'room_id' => $room->id
            ]);
        
        $response->assertSessionHasErrors();
        
        // Check that only one slot exists
        $this->assertEquals(1, TimetableSlot::count());
    }
}
