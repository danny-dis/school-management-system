<?php

namespace Tests\Feature\Api;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test the student index endpoint.
     *
     * @return void
     */
    public function testStudentIndex()
    {
        // Create an admin user
        $user = User::factory()->create([
            'role' => 'Admin'
        ]);

        // Authenticate the user
        Sanctum::actingAs($user);

        // Create some students
        Student::factory()->count(5)->create();

        // Make the request
        $response = $this->getJson('/api/admin/students');

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'phone_no',
                        'gender',
                        'dob'
                    ]
                ],
                'meta' => [
                    'total',
                    'count',
                    'per_page',
                    'current_page',
                    'total_pages',
                    'links'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Students retrieved successfully'
            ]);
    }

    /**
     * Test the student show endpoint.
     *
     * @return void
     */
    public function testStudentShow()
    {
        // Create an admin user
        $user = User::factory()->create([
            'role' => 'Admin'
        ]);

        // Authenticate the user
        Sanctum::actingAs($user);

        // Create a student
        $student = Student::factory()->create();

        // Make the request
        $response = $this->getJson("/api/admin/students/{$student->id}");

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone_no',
                    'gender',
                    'dob'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Student retrieved successfully',
                'data' => [
                    'id' => $student->id,
                    'name' => $student->name
                ]
            ]);
    }

    /**
     * Test the student store endpoint.
     *
     * @return void
     */
    public function testStudentStore()
    {
        // Create an admin user
        $user = User::factory()->create([
            'role' => 'Admin'
        ]);

        // Authenticate the user
        Sanctum::actingAs($user);

        // Create student data
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone_no' => $this->faker->phoneNumber,
            'dob' => $this->faker->date,
            'gender' => $this->faker->randomElement([1, 2]),
            'religion' => 1,
            'blood_group' => 1,
            'nationality' => $this->faker->country,
            'father_name' => $this->faker->name('male'),
            'father_phone_no' => $this->faker->phoneNumber,
            'mother_name' => $this->faker->name('female'),
            'mother_phone_no' => $this->faker->phoneNumber,
            'present_address' => $this->faker->address,
            'permanent_address' => $this->faker->address,
            'status' => 1,
            'username' => $this->faker->userName,
            'password' => 'password123'
        ];

        // Make the request
        $response = $this->postJson('/api/admin/students', $data);

        // Assert the response
        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Student created successfully',
                'data' => [
                    'name' => $data['name'],
                    'email' => $data['email']
                ]
            ]);

        // Assert the data was stored in the database
        $this->assertDatabaseHas('students', [
            'name' => $data['name'],
            'email' => $data['email']
        ]);
    }

    /**
     * Test the student update endpoint.
     *
     * @return void
     */
    public function testStudentUpdate()
    {
        // Create an admin user
        $user = User::factory()->create([
            'role' => 'Admin'
        ]);

        // Authenticate the user
        Sanctum::actingAs($user);

        // Create a student
        $student = Student::factory()->create();

        // Update data
        $data = [
            'name' => $this->faker->name,
            'phone_no' => $this->faker->phoneNumber,
            'present_address' => $this->faker->address
        ];

        // Make the request
        $response = $this->putJson("/api/admin/students/{$student->id}", $data);

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'phone_no',
                    'present_address'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Student updated successfully',
                'data' => [
                    'id' => $student->id,
                    'name' => $data['name'],
                    'phone_no' => $data['phone_no'],
                    'present_address' => $data['present_address']
                ]
            ]);

        // Assert the data was updated in the database
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => $data['name'],
            'phone_no' => $data['phone_no'],
            'present_address' => $data['present_address']
        ]);
    }

    /**
     * Test the student delete endpoint.
     *
     * @return void
     */
    public function testStudentDelete()
    {
        // Create an admin user
        $user = User::factory()->create([
            'role' => 'Admin'
        ]);

        // Authenticate the user
        Sanctum::actingAs($user);

        // Create a student
        $student = Student::factory()->create();

        // Make the request
        $response = $this->deleteJson("/api/admin/students/{$student->id}");

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Student deleted successfully',
                'data' => null
            ]);

        // Assert the data was deleted from the database
        $this->assertDatabaseMissing('students', [
            'id' => $student->id
        ]);
    }
}
