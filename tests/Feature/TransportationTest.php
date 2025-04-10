<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\UserRole;
use App\Vehicle;
use App\TransportRoute;
use App\TransportStop;
use App\TransportStudent;
use App\Registration;
use App\Employee;
use App\Http\Helpers\AppHelper;

class TransportationTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test vehicle creation.
     *
     * @return void
     */
    public function testVehicleCreation()
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
        
        // Create a driver
        $driverRole = UserRole::create([
            'name' => 'Driver',
            'deletable' => false
        ]);
        
        $driver = Employee::create([
            'name' => 'Test Driver',
            'designation' => 'Driver',
            'employee_id' => 'D101',
            'dob' => '1980-01-01',
            'gender' => 'Male',
            'religion' => 'Christianity',
            'email' => 'driver@example.com',
            'phone_no' => '1234567890',
            'address' => 'Test Address',
            'joining_date' => '2020-01-01',
            'role_id' => $driverRole->id
        ]);
        
        // Test vehicle creation
        $response = $this->actingAs($user)
            ->post(route('transportation.vehicles.store'), [
                'name' => 'Test Bus',
                'registration_no' => 'BUS-123',
                'type' => 'bus',
                'capacity' => 40,
                'driver_id' => $driver->id,
                'contact_no' => '1234567890',
                'description' => 'This is a test bus',
                'status' => AppHelper::ACTIVE
            ]);
        
        $response->assertRedirect(route('transportation.vehicles'));
        
        // Check if vehicle was created
        $this->assertDatabaseHas('vehicles', [
            'name' => 'Test Bus',
            'registration_no' => 'BUS-123',
            'type' => 'bus',
            'capacity' => 40,
            'driver_id' => $driver->id
        ]);
    }
    
    /**
     * Test route creation.
     *
     * @return void
     */
    public function testRouteCreation()
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
        
        // Create a vehicle
        $vehicle = Vehicle::create([
            'name' => 'Test Bus',
            'registration_no' => 'BUS-123',
            'type' => 'bus',
            'capacity' => 40,
            'status' => AppHelper::ACTIVE
        ]);
        
        // Test route creation
        $response = $this->actingAs($user)
            ->post(route('transportation.routes.store'), [
                'name' => 'Test Route',
                'vehicle_id' => $vehicle->id,
                'start_place' => 'School',
                'start_time' => '07:30',
                'end_place' => 'City Center',
                'end_time' => '08:30',
                'distance' => 10.5,
                'fare' => 50.00,
                'description' => 'This is a test route',
                'status' => AppHelper::ACTIVE
            ]);
        
        $route = TransportRoute::first();
        
        $response->assertRedirect(route('transportation.routes.stops', $route->id));
        
        // Check if route was created
        $this->assertDatabaseHas('transport_routes', [
            'name' => 'Test Route',
            'vehicle_id' => $vehicle->id,
            'start_place' => 'School',
            'start_time' => '07:30:00',
            'end_place' => 'City Center',
            'end_time' => '08:30:00',
            'distance' => 10.5,
            'fare' => 50.00
        ]);
    }
    
    /**
     * Test stop creation.
     *
     * @return void
     */
    public function testStopCreation()
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
        
        // Create a vehicle
        $vehicle = Vehicle::create([
            'name' => 'Test Bus',
            'registration_no' => 'BUS-123',
            'type' => 'bus',
            'capacity' => 40,
            'status' => AppHelper::ACTIVE
        ]);
        
        // Create a route
        $route = TransportRoute::create([
            'name' => 'Test Route',
            'vehicle_id' => $vehicle->id,
            'start_place' => 'School',
            'start_time' => '07:30',
            'end_place' => 'City Center',
            'end_time' => '08:30',
            'distance' => 10.5,
            'fare' => 50.00,
            'status' => AppHelper::ACTIVE
        ]);
        
        // Test stop creation
        $response = $this->actingAs($user)
            ->post(route('transportation.routes.stops.store', $route->id), [
                'name' => 'Test Stop',
                'stop_time' => '08:00',
                'stop_order' => 1,
                'latitude' => 12.345678,
                'longitude' => 98.765432,
                'description' => 'This is a test stop'
            ]);
        
        $response->assertRedirect(route('transportation.routes.stops', $route->id));
        
        // Check if stop was created
        $this->assertDatabaseHas('transport_stops', [
            'route_id' => $route->id,
            'name' => 'Test Stop',
            'stop_time' => '08:00:00',
            'stop_order' => 1,
            'latitude' => 12.345678,
            'longitude' => 98.765432
        ]);
    }
    
    /**
     * Test student assignment.
     *
     * @return void
     */
    public function testStudentAssignment()
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
        
        // Create a vehicle
        $vehicle = Vehicle::create([
            'name' => 'Test Bus',
            'registration_no' => 'BUS-123',
            'type' => 'bus',
            'capacity' => 40,
            'status' => AppHelper::ACTIVE
        ]);
        
        // Create a route
        $route = TransportRoute::create([
            'name' => 'Test Route',
            'vehicle_id' => $vehicle->id,
            'start_place' => 'School',
            'start_time' => '07:30',
            'end_place' => 'City Center',
            'end_time' => '08:30',
            'distance' => 10.5,
            'fare' => 50.00,
            'status' => AppHelper::ACTIVE
        ]);
        
        // Create a stop
        $stop = TransportStop::create([
            'route_id' => $route->id,
            'name' => 'Test Stop',
            'stop_time' => '08:00',
            'stop_order' => 1,
            'description' => 'This is a test stop'
        ]);
        
        // Create a student
        $student = factory(Registration::class)->create();
        
        // Test student assignment
        $response = $this->actingAs($user)
            ->post(route('transportation.students.store'), [
                'student_id' => $student->id,
                'route_id' => $route->id,
                'stop_id' => $stop->id,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addYear()->format('Y-m-d'),
                'status' => 'active',
                'notes' => 'This is a test assignment'
            ]);
        
        $response->assertRedirect(route('transportation.students'));
        
        // Check if assignment was created
        $this->assertDatabaseHas('transport_students', [
            'student_id' => $student->id,
            'route_id' => $route->id,
            'stop_id' => $stop->id,
            'status' => 'active'
        ]);
    }
}
