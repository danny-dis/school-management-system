<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\UserRole;
use App\Message;
use App\Notification;
use App\Announcement;
use App\EmailTemplate;
use App\SmsTemplate;
use App\Http\Helpers\AppHelper;

class CommunicationTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test message sending.
     *
     * @return void
     */
    public function testMessageSending()
    {
        // Create user roles
        $adminRole = UserRole::create([
            'name' => 'Admin',
            'deletable' => false
        ]);
        
        $teacherRole = UserRole::create([
            'name' => 'Teacher',
            'deletable' => false
        ]);
        
        // Create users
        $admin = User::create([
            'name' => 'Test Admin',
            'username' => 'testadmin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'status' => 1
        ]);
        
        $teacher = User::create([
            'name' => 'Test Teacher',
            'username' => 'testteacher',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
            'role_id' => $teacherRole->id,
            'status' => 1
        ]);
        
        // Test sending a message
        $response = $this->actingAs($admin)
            ->post(route('communication.message.store'), [
                'receiver_id' => $teacher->id,
                'subject' => 'Test Subject',
                'message' => 'This is a test message.'
            ]);
        
        $response->assertRedirect(route('communication.sent'));
        
        // Check if message was created
        $this->assertDatabaseHas('messages', [
            'sender_id' => $admin->id,
            'receiver_id' => $teacher->id,
            'subject' => 'Test Subject',
            'message' => 'This is a test message.'
        ]);
        
        // Check if notification was created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $teacher->id,
            'title' => 'New Message'
        ]);
    }
    
    /**
     * Test message viewing.
     *
     * @return void
     */
    public function testMessageViewing()
    {
        // Create user roles
        $adminRole = UserRole::create([
            'name' => 'Admin',
            'deletable' => false
        ]);
        
        $teacherRole = UserRole::create([
            'name' => 'Teacher',
            'deletable' => false
        ]);
        
        // Create users
        $admin = User::create([
            'name' => 'Test Admin',
            'username' => 'testadmin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'status' => 1
        ]);
        
        $teacher = User::create([
            'name' => 'Test Teacher',
            'username' => 'testteacher',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
            'role_id' => $teacherRole->id,
            'status' => 1
        ]);
        
        // Create a message
        $message = Message::create([
            'sender_id' => $admin->id,
            'receiver_id' => $teacher->id,
            'subject' => 'Test Subject',
            'message' => 'This is a test message.'
        ]);
        
        // Test viewing the message as receiver
        $response = $this->actingAs($teacher)
            ->get(route('communication.view', $message->id));
        
        $response->assertStatus(200)
            ->assertSee('Test Subject')
            ->assertSee('This is a test message.');
        
        // Check if message was marked as read
        $this->assertNotNull($message->fresh()->read_at);
    }
    
    /**
     * Test announcement creation.
     *
     * @return void
     */
    public function testAnnouncementCreation()
    {
        // Create user role
        $adminRole = UserRole::create([
            'name' => 'Admin',
            'deletable' => false
        ]);
        
        // Create user
        $admin = User::create([
            'name' => 'Test Admin',
            'username' => 'testadmin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'status' => 1
        ]);
        
        // Test creating an announcement
        $response = $this->actingAs($admin)
            ->post(route('communication.announcements.store'), [
                'title' => 'Test Announcement',
                'description' => 'This is a test announcement.',
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(7)->format('Y-m-d'),
                'for_students' => true,
                'for_teachers' => true,
                'for_parents' => true,
                'for_admins' => true,
                'status' => AppHelper::ACTIVE
            ]);
        
        $response->assertRedirect(route('communication.announcements'));
        
        // Check if announcement was created
        $this->assertDatabaseHas('announcements', [
            'title' => 'Test Announcement',
            'description' => 'This is a test announcement.',
            'created_by' => $admin->id,
            'status' => AppHelper::ACTIVE
        ]);
    }
    
    /**
     * Test email template creation.
     *
     * @return void
     */
    public function testEmailTemplateCreation()
    {
        // Create user role
        $adminRole = UserRole::create([
            'name' => 'Admin',
            'deletable' => false
        ]);
        
        // Create user
        $admin = User::create([
            'name' => 'Test Admin',
            'username' => 'testadmin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'status' => 1
        ]);
        
        // Test creating an email template
        $response = $this->actingAs($admin)
            ->post(route('communication.email_templates.store'), [
                'name' => 'Test Template',
                'subject' => 'Test Subject',
                'body' => 'This is a test email template with {variable1} and {variable2}.',
                'variables' => 'variable1, variable2',
                'status' => AppHelper::ACTIVE
            ]);
        
        $response->assertRedirect(route('communication.email_templates'));
        
        // Check if template was created
        $this->assertDatabaseHas('email_templates', [
            'name' => 'Test Template',
            'subject' => 'Test Subject',
            'status' => AppHelper::ACTIVE
        ]);
        
        // Get the template and check variables
        $template = EmailTemplate::where('name', 'Test Template')->first();
        $variables = json_decode($template->variables, true);
        
        $this->assertEquals(['variable1', 'variable2'], $variables);
    }
    
    /**
     * Test SMS template creation.
     *
     * @return void
     */
    public function testSmsTemplateCreation()
    {
        // Create user role
        $adminRole = UserRole::create([
            'name' => 'Admin',
            'deletable' => false
        ]);
        
        // Create user
        $admin = User::create([
            'name' => 'Test Admin',
            'username' => 'testadmin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
            'status' => 1
        ]);
        
        // Test creating an SMS template
        $response = $this->actingAs($admin)
            ->post(route('communication.sms_templates.store'), [
                'name' => 'Test SMS Template',
                'body' => 'This is a test SMS with {name} and {date}.',
                'variables' => 'name, date',
                'status' => AppHelper::ACTIVE
            ]);
        
        $response->assertRedirect(route('communication.sms_templates'));
        
        // Check if template was created
        $this->assertDatabaseHas('sms_templates', [
            'name' => 'Test SMS Template',
            'status' => AppHelper::ACTIVE
        ]);
    }
}
