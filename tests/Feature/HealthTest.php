<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\UserRole;
use App\HealthRecord;
use App\MedicalVisit;
use App\Vaccination;
use App\VaccinationRecord;
use App\Registration;
use App\Http\Helpers\AppHelper;

class HealthTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test health record creation.
     *
     * @return void
     */
    public function testHealthRecordCreation()
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
        
        // Create a student
        $student = factory(Registration::class)->create();
        
        // Test health record creation
        $response = $this->actingAs($user)
            ->post(route('health.store'), [
                'student_id' => $student->id,
                'height' => 170,
                'weight' => 65,
                'blood_group' => 'A+',
                'blood_pressure' => '120/80',
                'pulse_rate' => '72',
                'allergies' => 'None',
                'medications' => 'None',
                'past_medical_history' => 'None',
                'vision_left' => '6/6',
                'vision_right' => '6/6',
                'hearing_left' => 'Normal',
                'hearing_right' => 'Normal',
                'immunizations' => 'Up to date',
                'emergency_contact' => 'John Doe',
                'emergency_phone' => '1234567890',
                'notes' => 'This is a test health record'
            ]);
        
        $record = HealthRecord::first();
        
        $response->assertRedirect(route('health.show', $record->id));
        
        // Check if health record was created
        $this->assertDatabaseHas('health_records', [
            'student_id' => $student->id,
            'height' => 170,
            'weight' => 65,
            'bmi' => 22.49, // Calculated BMI
            'blood_group' => 'A+',
            'blood_pressure' => '120/80',
            'pulse_rate' => '72',
            'recorded_by' => $user->id
        ]);
    }
    
    /**
     * Test medical visit creation.
     *
     * @return void
     */
    public function testMedicalVisitCreation()
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
        
        // Create a student
        $student = factory(Registration::class)->create();
        
        // Create a health record
        $record = HealthRecord::create([
            'student_id' => $student->id,
            'height' => 170,
            'weight' => 65,
            'bmi' => 22.49,
            'blood_group' => 'A+',
            'recorded_by' => $user->id
        ]);
        
        // Test medical visit creation
        $response = $this->actingAs($user)
            ->post(route('health.visits.store', $record->id), [
                'visit_date' => now()->format('Y-m-d'),
                'symptoms' => 'Fever, Headache',
                'diagnosis' => 'Common Cold',
                'treatment' => 'Rest and fluids',
                'prescribed_medications' => 'Paracetamol',
                'temperature' => 38.5,
                'notes' => 'This is a test medical visit',
                'follow_up_date' => now()->addDays(3)->format('Y-m-d')
            ]);
        
        $response->assertRedirect(route('health.show', $record->id));
        
        // Check if medical visit was created
        $this->assertDatabaseHas('medical_visits', [
            'student_id' => $student->id,
            'health_record_id' => $record->id,
            'symptoms' => 'Fever, Headache',
            'diagnosis' => 'Common Cold',
            'treatment' => 'Rest and fluids',
            'prescribed_medications' => 'Paracetamol',
            'temperature' => 38.5,
            'attended_by' => $user->id
        ]);
    }
    
    /**
     * Test vaccination creation.
     *
     * @return void
     */
    public function testVaccinationCreation()
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
        
        // Test vaccination creation
        $response = $this->actingAs($user)
            ->post(route('health.vaccinations.store'), [
                'name' => 'Test Vaccination',
                'description' => 'This is a test vaccination',
                'recommended_age' => '10-12 years',
                'doses' => 2,
                'status' => AppHelper::ACTIVE
            ]);
        
        $response->assertRedirect(route('health.vaccinations'));
        
        // Check if vaccination was created
        $this->assertDatabaseHas('vaccinations', [
            'name' => 'Test Vaccination',
            'description' => 'This is a test vaccination',
            'recommended_age' => '10-12 years',
            'doses' => 2,
            'status' => AppHelper::ACTIVE
        ]);
    }
    
    /**
     * Test vaccination record creation.
     *
     * @return void
     */
    public function testVaccinationRecordCreation()
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
        
        // Create a student
        $student = factory(Registration::class)->create();
        
        // Create a health record
        $record = HealthRecord::create([
            'student_id' => $student->id,
            'height' => 170,
            'weight' => 65,
            'bmi' => 22.49,
            'blood_group' => 'A+',
            'recorded_by' => $user->id
        ]);
        
        // Create a vaccination
        $vaccination = Vaccination::create([
            'name' => 'Test Vaccination',
            'description' => 'This is a test vaccination',
            'recommended_age' => '10-12 years',
            'doses' => 2,
            'status' => AppHelper::ACTIVE
        ]);
        
        // Test vaccination record creation
        $response = $this->actingAs($user)
            ->post(route('health.vaccinations.record.store', $record->id), [
                'vaccination_id' => $vaccination->id,
                'dose_number' => 1,
                'date_given' => now()->format('Y-m-d'),
                'next_due_date' => now()->addMonths(6)->format('Y-m-d'),
                'administered_by' => 'Dr. John Doe',
                'notes' => 'This is a test vaccination record'
            ]);
        
        $response->assertRedirect(route('health.show', $record->id));
        
        // Check if vaccination record was created
        $this->assertDatabaseHas('vaccination_records', [
            'student_id' => $student->id,
            'vaccination_id' => $vaccination->id,
            'dose_number' => 1,
            'administered_by' => 'Dr. John Doe',
            'recorded_by' => $user->id
        ]);
    }
}
