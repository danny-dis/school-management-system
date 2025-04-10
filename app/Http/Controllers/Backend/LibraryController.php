<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Book;
use App\BookCategory;
use App\BookIssue;
use App\LibrarySetting;
use App\Registration;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * LibraryController
 * 
 * This controller handles the library management module functionality.
 */
class LibraryController extends Controller
{
    /**
     * Display a listing of books.
     *
     * @return \Illuminate\Http\Response
     */
    public function books()
    {
        $books = Book::with('category')->orderBy('id', 'desc')->paginate(10);
        return view('backend.library.books.index', compact('books'));
    }

    /**
     * Show the form for creating a new book.
     *
     * @return \Illuminate\Http\Response
     */
    public function createBook()
    {
        $categories = BookCategory::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        
        return view('backend.library.books.create', compact('categories'));
    }

    /**
     * Store a newly created book in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeBook(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:30',
            'author' => 'nullable|string|max:100',
            'publisher' => 'nullable|string|max:100',
            'edition' => 'nullable|string|max:50',
            'category_id' => 'required|integer|exists:book_categories,id',
            'description' => 'nullable|string',
            'publish_year' => 'nullable|string|max:4',
            'quantity' => 'required|integer|min:1',
            'rack_no' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|integer'
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/books');
            $imagePath = basename($imagePath);
        }

        // Create book
        $book = Book::create([
            'title' => $request->title,
            'isbn' => $request->isbn,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'edition' => $request->edition,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'publish_year' => $request->publish_year,
            'quantity' => $request->quantity,
            'available' => $request->quantity,
            'rack_no' => $request->rack_no,
            'image' => $imagePath,
            'status' => $request->status
        ]);

        return redirect()->route('library.books')->with('success', 'Book created successfully!');
    }

    /**
     * Display the specified book.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showBook($id)
    {
        $book = Book::with('category', 'issues.student.student')->findOrFail($id);
        
        return view('backend.library.books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified book.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editBook($id)
    {
        $book = Book::findOrFail($id);
        $categories = BookCategory::where('status', AppHelper::ACTIVE)->pluck('name', 'id');
        
        return view('backend.library.books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified book in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateBook(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:30',
            'author' => 'nullable|string|max:100',
            'publisher' => 'nullable|string|max:100',
            'edition' => 'nullable|string|max:50',
            'category_id' => 'required|integer|exists:book_categories,id',
            'description' => 'nullable|string',
            'publish_year' => 'nullable|string|max:4',
            'quantity' => 'required|integer|min:' . ($book->quantity - $book->available),
            'rack_no' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|integer'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($book->image) {
                Storage::delete('public/books/'.$book->image);
            }
            
            $imagePath = $request->file('image')->store('public/books');
            $book->image = basename($imagePath);
        }

        // Calculate available books
        $additionalBooks = $request->quantity - $book->quantity;
        $newAvailable = $book->available + $additionalBooks;
        
        // Update book
        $book->title = $request->title;
        $book->isbn = $request->isbn;
        $book->author = $request->author;
        $book->publisher = $request->publisher;
        $book->edition = $request->edition;
        $book->category_id = $request->category_id;
        $book->description = $request->description;
        $book->publish_year = $request->publish_year;
        $book->quantity = $request->quantity;
        $book->available = $newAvailable;
        $book->rack_no = $request->rack_no;
        $book->status = $request->status;
        $book->save();

        return redirect()->route('library.books')->with('success', 'Book updated successfully!');
    }

    /**
     * Remove the specified book from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyBook($id)
    {
        $book = Book::findOrFail($id);
        
        // Check if book has active issues
        $activeIssues = $book->issues()->whereIn('status', [BookIssue::STATUS_ISSUED, BookIssue::STATUS_OVERDUE])->count();
        if ($activeIssues > 0) {
            return redirect()->route('library.books')->with('error', 'Cannot delete book with active issues!');
        }
        
        // Delete image if exists
        if ($book->image) {
            Storage::delete('public/books/'.$book->image);
        }
        
        $book->delete();
        
        return redirect()->route('library.books')->with('success', 'Book deleted successfully!');
    }

    /**
     * Display a listing of book categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function categories()
    {
        $categories = BookCategory::withCount('books')->orderBy('id', 'desc')->paginate(10);
        return view('backend.library.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new book category.
     *
     * @return \Illuminate\Http\Response
     */
    public function createCategory()
    {
        return view('backend.library.categories.create');
    }

    /**
     * Store a newly created book category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCategory(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        // Create category
        $category = BookCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status
        ]);

        return redirect()->route('library.categories')->with('success', 'Category created successfully!');
    }

    /**
     * Show the form for editing the specified book category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editCategory($id)
    {
        $category = BookCategory::findOrFail($id);
        
        return view('backend.library.categories.edit', compact('category'));
    }

    /**
     * Update the specified book category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateCategory(Request $request, $id)
    {
        $category = BookCategory::findOrFail($id);
        
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        // Update category
        $category->name = $request->name;
        $category->description = $request->description;
        $category->status = $request->status;
        $category->save();

        return redirect()->route('library.categories')->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified book category from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyCategory($id)
    {
        $category = BookCategory::findOrFail($id);
        
        // Check if category has books
        if ($category->books()->count() > 0) {
            return redirect()->route('library.categories')->with('error', 'Cannot delete category with existing books!');
        }
        
        $category->delete();
        
        return redirect()->route('library.categories')->with('success', 'Category deleted successfully!');
    }

    /**
     * Display a listing of book issues.
     *
     * @return \Illuminate\Http\Response
     */
    public function issues()
    {
        $issues = BookIssue::with('book', 'student.student')
            ->orderBy('id', 'desc')
            ->paginate(10);
            
        return view('backend.library.issues.index', compact('issues'));
    }

    /**
     * Show the form for creating a new book issue.
     *
     * @return \Illuminate\Http\Response
     */
    public function createIssue()
    {
        $books = Book::where('status', AppHelper::ACTIVE)
            ->where('available', '>', 0)
            ->pluck('title', 'id');
            
        $settings = LibrarySetting::getSettings();
        
        return view('backend.library.issues.create', compact('books', 'settings'));
    }

    /**
     * Get student details for book issue.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getStudentDetails(Request $request)
    {
        $studentId = $request->student_id;
        
        $student = Registration::with('student', 'class', 'section')
            ->where('id', $studentId)
            ->where('status', AppHelper::ACTIVE)
            ->first();
            
        if (!$student) {
            return response()->json(['error' => 'Student not found or inactive'], 404);
        }
        
        // Get active issues count
        $activeIssuesCount = BookIssue::where('student_id', $studentId)
            ->whereIn('status', [BookIssue::STATUS_ISSUED, BookIssue::STATUS_OVERDUE])
            ->count();
            
        // Get settings
        $settings = LibrarySetting::getSettings();
        $maxBooks = $settings->max_books_per_student;
        
        return response()->json([
            'student' => [
                'id' => $student->id,
                'name' => $student->student->name,
                'class' => $student->class->name,
                'section' => $student->section->name,
                'roll' => $student->roll_no
            ],
            'active_issues' => $activeIssuesCount,
            'can_issue' => $activeIssuesCount < $maxBooks
        ]);
    }

    /**
     * Store a newly created book issue in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeIssue(Request $request)
    {
        $this->validate($request, [
            'book_id' => 'required|integer|exists:books,id',
            'student_id' => 'required|integer|exists:registrations,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'notes' => 'nullable|string'
        ]);

        // Get book and check availability
        $book = Book::findOrFail($request->book_id);
        if ($book->available <= 0 || $book->status != AppHelper::ACTIVE) {
            return redirect()->back()->with('error', 'Book is not available for issue!')->withInput();
        }
        
        // Get student and check eligibility
        $student = Registration::findOrFail($request->student_id);
        if ($student->status != AppHelper::ACTIVE) {
            return redirect()->back()->with('error', 'Student is not active!')->withInput();
        }
        
        // Get settings
        $settings = LibrarySetting::getSettings();
        $maxBooks = $settings->max_books_per_student;
        
        // Check if student has reached maximum books limit
        $activeIssuesCount = BookIssue::where('student_id', $request->student_id)
            ->whereIn('status', [BookIssue::STATUS_ISSUED, BookIssue::STATUS_OVERDUE])
            ->count();
            
        if ($activeIssuesCount >= $maxBooks) {
            return redirect()->back()->with('error', 'Student has reached maximum books limit!')->withInput();
        }
        
        // Create issue
        DB::transaction(function () use ($request, $book) {
            // Create issue record
            $issue = BookIssue::create([
                'book_id' => $request->book_id,
                'student_id' => $request->student_id,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'status' => BookIssue::STATUS_ISSUED,
                'notes' => $request->notes,
                'issued_by' => Auth::id()
            ]);
            
            // Update book availability
            $book->available = $book->available - 1;
            $book->save();
        });

        return redirect()->route('library.issues')->with('success', 'Book issued successfully!');
    }

    /**
     * Display the specified book issue.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showIssue($id)
    {
        $issue = BookIssue::with('book', 'student.student', 'issuedBy', 'returnedBy')
            ->findOrFail($id);
            
        $settings = LibrarySetting::getSettings();
        
        return view('backend.library.issues.show', compact('issue', 'settings'));
    }

    /**
     * Show the form for returning a book.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function returnBook($id)
    {
        $issue = BookIssue::with('book', 'student.student')
            ->findOrFail($id);
            
        // Check if book is already returned
        if ($issue->status == BookIssue::STATUS_RETURNED) {
            return redirect()->route('library.issues.show', $id)
                ->with('error', 'Book is already returned!');
        }
        
        $settings = LibrarySetting::getSettings();
        $finePerDay = $settings->fine_per_day;
        
        // Calculate fine if overdue
        $fine = 0;
        if ($issue->due_date < now()) {
            $daysLate = now()->diffInDays($issue->due_date);
            $fine = $daysLate * $finePerDay;
        }
        
        return view('backend.library.issues.return', compact('issue', 'fine'));
    }

    /**
     * Process the book return.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function processReturn(Request $request, $id)
    {
        $issue = BookIssue::findOrFail($id);
        
        // Check if book is already returned
        if ($issue->status == BookIssue::STATUS_RETURNED) {
            return redirect()->route('library.issues.show', $id)
                ->with('error', 'Book is already returned!');
        }
        
        $this->validate($request, [
            'return_date' => 'required|date',
            'fine_amount' => 'required|numeric|min:0',
            'fine_paid' => 'required|numeric|min:0|max:'.$request->fine_amount,
            'status' => 'required|string|in:returned,lost',
            'notes' => 'nullable|string'
        ]);

        // Process return
        DB::transaction(function () use ($request, $issue) {
            // Update issue record
            $issue->return_date = $request->return_date;
            $issue->fine_amount = $request->fine_amount;
            $issue->fine_paid = $request->fine_paid;
            $issue->status = $request->status;
            $issue->notes = $request->notes;
            $issue->returned_by = Auth::id();
            $issue->save();
            
            // Update book availability if not lost
            if ($request->status == BookIssue::STATUS_RETURNED) {
                $book = $issue->book;
                $book->available = $book->available + 1;
                $book->save();
            }
        });

        return redirect()->route('library.issues.show', $id)->with('success', 'Book return processed successfully!');
    }

    /**
     * Mark a book as lost.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markLost($id)
    {
        $issue = BookIssue::findOrFail($id);
        
        // Check if book is already returned
        if ($issue->status == BookIssue::STATUS_RETURNED || $issue->status == BookIssue::STATUS_LOST) {
            return redirect()->route('library.issues.show', $id)
                ->with('error', 'Book is already returned or marked as lost!');
        }
        
        $issue->status = BookIssue::STATUS_LOST;
        $issue->save();
        
        return redirect()->route('library.issues.show', $id)->with('success', 'Book marked as lost successfully!');
    }

    /**
     * Display the library settings.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {
        $settings = LibrarySetting::getSettings();
        
        return view('backend.library.settings', compact('settings'));
    }

    /**
     * Update the library settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateSettings(Request $request)
    {
        $this->validate($request, [
            'max_books_per_student' => 'required|integer|min:1',
            'max_days_per_issue' => 'required|integer|min:1',
            'fine_per_day' => 'required|numeric|min:0',
            'allow_renewal' => 'nullable|boolean',
            'max_renewals' => 'required|integer|min:0'
        ]);

        $settings = LibrarySetting::getSettings();
        
        // Update settings
        $settings->max_books_per_student = $request->max_books_per_student;
        $settings->max_days_per_issue = $request->max_days_per_issue;
        $settings->fine_per_day = $request->fine_per_day;
        $settings->allow_renewal = $request->has('allow_renewal');
        $settings->max_renewals = $request->max_renewals;
        $settings->save();

        return redirect()->route('library.settings')->with('success', 'Library settings updated successfully!');
    }

    /**
     * Display the fine collection report.
     *
     * @return \Illuminate\Http\Response
     */
    public function fineCollection()
    {
        $fineCollected = BookIssue::where('fine_paid', '>', 0)
            ->sum('fine_paid');
            
        $finePending = BookIssue::whereRaw('fine_amount > fine_paid')
            ->sum(DB::raw('fine_amount - fine_paid'));
            
        $recentCollections = BookIssue::with('student.student', 'book')
            ->where('fine_paid', '>', 0)
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();
            
        return view('backend.library.fine_collection', compact('fineCollected', 'finePending', 'recentCollections'));
    }

    /**
     * Display the overdue books report.
     *
     * @return \Illuminate\Http\Response
     */
    public function overdueBooks()
    {
        $overdueIssues = BookIssue::with('book', 'student.student')
            ->where('status', BookIssue::STATUS_ISSUED)
            ->where('due_date', '<', now())
            ->orderBy('due_date', 'asc')
            ->paginate(10);
            
        return view('backend.library.overdue_books', compact('overdueIssues'));
    }
}
