<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Book;
use App\BookCategory;
use App\BookIssue;
use App\LibrarySetting;
use App\Registration;
use App\Http\Helpers\AppHelper;

class LibraryManagementTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test book category creation.
     *
     * @return void
     */
    public function testBookCategoryCreation()
    {
        // Create a user with appropriate permissions
        $user = factory(User::class)->create();
        $this->actingAs($user);
        
        // Test category creation
        $response = $this->post(route('library.categories.store'), [
            'name' => 'Test Category',
            'description' => 'This is a test category',
            'status' => AppHelper::ACTIVE
        ]);
        
        $response->assertRedirect(route('library.categories'));
        $this->assertDatabaseHas('book_categories', [
            'name' => 'Test Category',
            'description' => 'This is a test category'
        ]);
    }
    
    /**
     * Test book creation.
     *
     * @return void
     */
    public function testBookCreation()
    {
        // Create a user with appropriate permissions
        $user = factory(User::class)->create();
        $this->actingAs($user);
        
        // Create a category for the book
        $category = factory(BookCategory::class)->create();
        
        // Test book creation
        $response = $this->post(route('library.books.store'), [
            'title' => 'Test Book',
            'isbn' => '1234567890',
            'author' => 'Test Author',
            'publisher' => 'Test Publisher',
            'category_id' => $category->id,
            'quantity' => 5,
            'status' => AppHelper::ACTIVE
        ]);
        
        $response->assertRedirect(route('library.books'));
        $this->assertDatabaseHas('books', [
            'title' => 'Test Book',
            'isbn' => '1234567890',
            'author' => 'Test Author',
            'quantity' => 5,
            'available' => 5
        ]);
    }
    
    /**
     * Test book issue.
     *
     * @return void
     */
    public function testBookIssue()
    {
        // Create a user with appropriate permissions
        $user = factory(User::class)->create();
        $this->actingAs($user);
        
        // Create necessary data
        $category = factory(BookCategory::class)->create();
        $book = factory(Book::class)->create([
            'category_id' => $category->id,
            'quantity' => 5,
            'available' => 5
        ]);
        $student = factory(Registration::class)->create();
        
        // Create library settings
        LibrarySetting::create([
            'max_books_per_student' => 2,
            'max_days_per_issue' => 14,
            'fine_per_day' => 1.00,
            'allow_renewal' => true,
            'max_renewals' => 1
        ]);
        
        // Test book issue
        $response = $this->post(route('library.issues.store'), [
            'book_id' => $book->id,
            'student_id' => $student->id,
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'notes' => 'Test issue'
        ]);
        
        $response->assertRedirect(route('library.issues'));
        $this->assertDatabaseHas('book_issues', [
            'book_id' => $book->id,
            'student_id' => $student->id,
            'status' => BookIssue::STATUS_ISSUED
        ]);
        
        // Check if book availability is updated
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'available' => 4
        ]);
    }
    
    /**
     * Test book return.
     *
     * @return void
     */
    public function testBookReturn()
    {
        // Create a user with appropriate permissions
        $user = factory(User::class)->create();
        $this->actingAs($user);
        
        // Create necessary data
        $category = factory(BookCategory::class)->create();
        $book = factory(Book::class)->create([
            'category_id' => $category->id,
            'quantity' => 5,
            'available' => 4
        ]);
        $student = factory(Registration::class)->create();
        
        // Create library settings
        LibrarySetting::create([
            'max_books_per_student' => 2,
            'max_days_per_issue' => 14,
            'fine_per_day' => 1.00,
            'allow_renewal' => true,
            'max_renewals' => 1
        ]);
        
        // Create a book issue
        $issue = BookIssue::create([
            'book_id' => $book->id,
            'student_id' => $student->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'status' => BookIssue::STATUS_ISSUED,
            'issued_by' => $user->id
        ]);
        
        // Test book return
        $response = $this->post(route('library.issues.process_return', $issue->id), [
            'return_date' => now()->format('Y-m-d'),
            'fine_amount' => 0,
            'fine_paid' => 0,
            'status' => BookIssue::STATUS_RETURNED,
            'notes' => 'Test return'
        ]);
        
        $response->assertRedirect(route('library.issues.show', $issue->id));
        $this->assertDatabaseHas('book_issues', [
            'id' => $issue->id,
            'status' => BookIssue::STATUS_RETURNED
        ]);
        
        // Check if book availability is updated
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'available' => 5
        ]);
    }
    
    /**
     * Test library settings update.
     *
     * @return void
     */
    public function testLibrarySettingsUpdate()
    {
        // Create a user with appropriate permissions
        $user = factory(User::class)->create();
        $this->actingAs($user);
        
        // Create library settings
        LibrarySetting::create([
            'max_books_per_student' => 2,
            'max_days_per_issue' => 14,
            'fine_per_day' => 1.00,
            'allow_renewal' => true,
            'max_renewals' => 1
        ]);
        
        // Test settings update
        $response = $this->post(route('library.settings.update'), [
            'max_books_per_student' => 3,
            'max_days_per_issue' => 21,
            'fine_per_day' => 2.00,
            'allow_renewal' => true,
            'max_renewals' => 2
        ]);
        
        $response->assertRedirect(route('library.settings'));
        $this->assertDatabaseHas('library_settings', [
            'max_books_per_student' => 3,
            'max_days_per_issue' => 21,
            'fine_per_day' => 2.00,
            'max_renewals' => 2
        ]);
    }
}
