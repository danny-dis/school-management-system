<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookIssue;
use App\Models\LibrarySetting;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class LibraryService
{
    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * LibraryService constructor.
     *
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create a book category
     *
     * @param array $data
     * @param int $createdBy
     * @return BookCategory|null
     */
    public function createCategory(array $data, $createdBy)
    {
        try {
            return BookCategory::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? BookCategory::STATUS_ACTIVE,
                'created_by' => $createdBy
            ]);
        } catch (Exception $e) {
            Log::error('Error creating book category: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a book category
     *
     * @param int $id
     * @param array $data
     * @return BookCategory|null
     */
    public function updateCategory($id, array $data)
    {
        try {
            $category = BookCategory::find($id);
            
            if (!$category) {
                return null;
            }
            
            $category->update($data);
            return $category;
        } catch (Exception $e) {
            Log::error('Error updating book category: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a book category
     *
     * @param int $id
     * @return bool
     */
    public function deleteCategory($id)
    {
        try {
            $category = BookCategory::find($id);
            
            if (!$category) {
                return false;
            }
            
            // Check if category has books
            $bookCount = Book::where('category_id', $id)->count();
            
            if ($bookCount > 0) {
                // Don't delete, just mark as inactive
                $category->status = BookCategory::STATUS_INACTIVE;
                $category->save();
            } else {
                $category->delete();
            }
            
            return true;
        } catch (Exception $e) {
            Log::error('Error deleting book category: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Create a book
     *
     * @param array $data
     * @param int $createdBy
     * @return Book|null
     */
    public function createBook(array $data, $createdBy)
    {
        try {
            return Book::create([
                'title' => $data['title'],
                'isbn' => $data['isbn'] ?? null,
                'author' => $data['author'] ?? null,
                'publisher' => $data['publisher'] ?? null,
                'edition' => $data['edition'] ?? null,
                'category_id' => $data['category_id'],
                'description' => $data['description'] ?? null,
                'publish_year' => $data['publish_year'] ?? null,
                'quantity' => $data['quantity'] ?? 1,
                'available' => $data['quantity'] ?? 1,
                'rack_no' => $data['rack_no'] ?? null,
                'image' => $data['image'] ?? null,
                'status' => $data['status'] ?? Book::STATUS_ACTIVE,
                'created_by' => $createdBy
            ]);
        } catch (Exception $e) {
            Log::error('Error creating book: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update a book
     *
     * @param int $id
     * @param array $data
     * @return Book|null
     */
    public function updateBook($id, array $data)
    {
        try {
            DB::beginTransaction();
            
            $book = Book::find($id);
            
            if (!$book) {
                return null;
            }
            
            // Handle quantity change
            if (isset($data['quantity'])) {
                $quantityDiff = $data['quantity'] - $book->quantity;
                $data['available'] = $book->available + $quantityDiff;
                
                // Ensure available is not negative
                if ($data['available'] < 0) {
                    $data['available'] = 0;
                }
            }
            
            $book->update($data);
            
            DB::commit();
            return $book;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating book: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete a book
     *
     * @param int $id
     * @return bool
     */
    public function deleteBook($id)
    {
        try {
            $book = Book::find($id);
            
            if (!$book) {
                return false;
            }
            
            // Check if book has issues
            $issueCount = BookIssue::where('book_id', $id)->count();
            
            if ($issueCount > 0) {
                // Don't delete, just mark as inactive
                $book->status = Book::STATUS_INACTIVE;
                $book->save();
            } else {
                $book->delete();
            }
            
            return true;
        } catch (Exception $e) {
            Log::error('Error deleting book: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Issue a book
     *
     * @param array $data
     * @param int $issuedBy
     * @return BookIssue|null
     */
    public function issueBook(array $data, $issuedBy)
    {
        try {
            DB::beginTransaction();
            
            $book = Book::find($data['book_id']);
            $student = Student::find($data['student_id']);
            
            if (!$book || !$student) {
                return null;
            }
            
            // Check if book is available
            if ($book->available <= 0) {
                throw new Exception('Book is not available for issue');
            }
            
            // Check if student has reached maximum books limit
            $settings = $this->getLibrarySettings();
            $issuedBooksCount = BookIssue::where('student_id', $student->id)
                ->whereNull('returned_at')
                ->count();
            
            if ($issuedBooksCount >= $settings->max_books_per_student) {
                throw new Exception('Student has reached maximum books limit');
            }
            
            // Calculate due date
            $issueDate = Carbon::parse($data['issue_date'] ?? now());
            $dueDate = $issueDate->copy()->addDays($settings->max_days_per_issue);
            
            // Create issue record
            $issue = BookIssue::create([
                'book_id' => $book->id,
                'student_id' => $student->id,
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'note' => $data['note'] ?? null,
                'issued_by' => $issuedBy
            ]);
            
            // Update book available count
            $book->available -= 1;
            $book->save();
            
            // Notify student about book issue
            if ($student->user_id) {
                $this->notificationService->createNotification(
                    $student->user_id,
                    'Book Issued',
                    'You have been issued the book: ' . $book->title,
                    route('library.issues.show', $issue->id),
                    'info'
                );
            }
            
            DB::commit();
            return $issue;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error issuing book: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Return a book
     *
     * @param int $issueId
     * @param array $data
     * @param int $returnedBy
     * @return BookIssue|null
     */
    public function returnBook($issueId, array $data, $returnedBy)
    {
        try {
            DB::beginTransaction();
            
            $issue = BookIssue::find($issueId);
            
            if (!$issue) {
                return null;
            }
            
            // Check if book is already returned
            if ($issue->returned_at) {
                throw new Exception('Book is already returned');
            }
            
            // Calculate fine if returned late
            $returnDate = Carbon::parse($data['return_date'] ?? now());
            $dueDate = Carbon::parse($issue->due_date);
            $fine = 0;
            
            if ($returnDate->gt($dueDate)) {
                $daysLate = $returnDate->diffInDays($dueDate);
                $settings = $this->getLibrarySettings();
                $fine = $daysLate * $settings->fine_per_day;
            }
            
            // Update issue record
            $issue->returned_at = $returnDate;
            $issue->fine = $fine;
            $issue->fine_paid = $data['fine_paid'] ?? false;
            $issue->return_note = $data['return_note'] ?? null;
            $issue->returned_by = $returnedBy;
            $issue->save();
            
            // Update book available count
            $book = $issue->book;
            $book->available += 1;
            $book->save();
            
            // Notify student about book return
            $student = $issue->student;
            if ($student && $student->user_id) {
                $message = 'You have returned the book: ' . $book->title;
                
                if ($fine > 0) {
                    $message .= ' with a fine of ' . $fine;
                }
                
                $this->notificationService->createNotification(
                    $student->user_id,
                    'Book Returned',
                    $message,
                    route('library.issues.show', $issue->id),
                    'info'
                );
            }
            
            DB::commit();
            return $issue;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error returning book: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Renew a book
     *
     * @param int $issueId
     * @param array $data
     * @param int $renewedBy
     * @return BookIssue|null
     */
    public function renewBook($issueId, array $data, $renewedBy)
    {
        try {
            DB::beginTransaction();
            
            $issue = BookIssue::find($issueId);
            
            if (!$issue) {
                return null;
            }
            
            // Check if book is already returned
            if ($issue->returned_at) {
                throw new Exception('Book is already returned');
            }
            
            // Check if renewal is allowed
            $settings = $this->getLibrarySettings();
            
            if (!$settings->allow_renewal) {
                throw new Exception('Book renewal is not allowed');
            }
            
            // Check if maximum renewals reached
            if ($issue->renewal_count >= $settings->max_renewals) {
                throw new Exception('Maximum renewals reached');
            }
            
            // Calculate new due date
            $newDueDate = Carbon::parse($issue->due_date)->addDays($settings->max_days_per_issue);
            
            // Update issue record
            $issue->due_date = $newDueDate;
            $issue->renewal_count += 1;
            $issue->renewal_date = now();
            $issue->renewal_note = $data['renewal_note'] ?? null;
            $issue->renewed_by = $renewedBy;
            $issue->save();
            
            // Notify student about book renewal
            $student = $issue->student;
            if ($student && $student->user_id) {
                $this->notificationService->createNotification(
                    $student->user_id,
                    'Book Renewed',
                    'Your book has been renewed: ' . $issue->book->title . '. New due date: ' . $newDueDate->format('M d, Y'),
                    route('library.issues.show', $issue->id),
                    'info'
                );
            }
            
            DB::commit();
            return $issue;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error renewing book: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update library settings
     *
     * @param array $data
     * @return LibrarySetting|null
     */
    public function updateSettings(array $data)
    {
        try {
            $settings = $this->getLibrarySettings();
            $settings->update($data);
            return $settings;
        } catch (Exception $e) {
            Log::error('Error updating library settings: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Get library settings
     *
     * @return LibrarySetting
     */
    public function getLibrarySettings()
    {
        $settings = LibrarySetting::first();
        
        if (!$settings) {
            $settings = LibrarySetting::create([
                'max_books_per_student' => 2,
                'max_days_per_issue' => 14,
                'fine_per_day' => 1.00,
                'allow_renewal' => true,
                'max_renewals' => 1
            ]);
        }
        
        return $settings;
    }

    /**
     * Get book categories
     *
     * @param bool $activeOnly
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCategories($activeOnly = false)
    {
        $query = BookCategory::query();
        
        if ($activeOnly) {
            $query->where('status', BookCategory::STATUS_ACTIVE);
        }
        
        return $query->orderBy('name', 'asc')->get();
    }

    /**
     * Get books
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getBooks(array $filters = [], $perPage = 15)
    {
        $query = Book::with('category');
        
        if (isset($filters['category_id']) && $filters['category_id']) {
            $query->where('category_id', $filters['category_id']);
        }
        
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }
        
        return $query->orderBy('title', 'asc')->paginate($perPage);
    }

    /**
     * Get book issues
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getIssues(array $filters = [], $perPage = 15)
    {
        $query = BookIssue::with(['book', 'student', 'issuedByUser', 'returnedByUser']);
        
        if (isset($filters['student_id']) && $filters['student_id']) {
            $query->where('student_id', $filters['student_id']);
        }
        
        if (isset($filters['book_id']) && $filters['book_id']) {
            $query->where('book_id', $filters['book_id']);
        }
        
        if (isset($filters['status']) && $filters['status'] !== '') {
            if ($filters['status'] == 'issued') {
                $query->whereNull('returned_at');
            } elseif ($filters['status'] == 'returned') {
                $query->whereNotNull('returned_at');
            } elseif ($filters['status'] == 'overdue') {
                $query->whereNull('returned_at')
                    ->where('due_date', '<', now());
            }
        }
        
        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->where('issue_date', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->where('issue_date', '<=', $filters['date_to']);
        }
        
        return $query->orderBy('issue_date', 'desc')->paginate($perPage);
    }

    /**
     * Get overdue books
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOverdueBooks()
    {
        return BookIssue::with(['book', 'student'])
            ->whereNull('returned_at')
            ->where('due_date', '<', now())
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Get books issued to a student
     *
     * @param int $studentId
     * @param bool $currentOnly
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBooksIssuedToStudent($studentId, $currentOnly = true)
    {
        $query = BookIssue::with('book')
            ->where('student_id', $studentId);
        
        if ($currentOnly) {
            $query->whereNull('returned_at');
        }
        
        return $query->orderBy('issue_date', 'desc')->get();
    }

    /**
     * Search books
     *
     * @param string $search
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function searchBooks($search, $perPage = 15)
    {
        return Book::with('category')
            ->where('status', Book::STATUS_ACTIVE)
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%");
            })
            ->orderBy('title', 'asc')
            ->paginate($perPage);
    }
}
