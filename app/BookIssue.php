<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BookIssue
 * 
 * This model represents a book issue in the library management system.
 * 
 * @package App
 */
class BookIssue extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'book_id', 
        'student_id', 
        'issue_date', 
        'due_date', 
        'return_date', 
        'fine_amount',
        'fine_paid',
        'status',
        'notes',
        'issued_by',
        'returned_by'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'issue_date',
        'due_date',
        'return_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Status constants
     */
    const STATUS_ISSUED = 'issued';
    const STATUS_RETURNED = 'returned';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_LOST = 'lost';

    /**
     * Get the book that owns the issue.
     */
    public function book()
    {
        return $this->belongsTo('App\Book');
    }

    /**
     * Get the student that owns the issue.
     */
    public function student()
    {
        return $this->belongsTo('App\Registration', 'student_id');
    }

    /**
     * Get the user who issued the book.
     */
    public function issuedBy()
    {
        return $this->belongsTo('App\User', 'issued_by');
    }

    /**
     * Get the user who received the returned book.
     */
    public function returnedBy()
    {
        return $this->belongsTo('App\User', 'returned_by');
    }

    /**
     * Get the status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case self::STATUS_ISSUED:
                return 'Issued';
            case self::STATUS_RETURNED:
                return 'Returned';
            case self::STATUS_OVERDUE:
                return 'Overdue';
            case self::STATUS_LOST:
                return 'Lost';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get the status color class.
     *
     * @return string
     */
    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case self::STATUS_ISSUED:
                return 'info';
            case self::STATUS_RETURNED:
                return 'success';
            case self::STATUS_OVERDUE:
                return 'warning';
            case self::STATUS_LOST:
                return 'danger';
            default:
                return 'default';
        }
    }

    /**
     * Check if the book is overdue.
     *
     * @return bool
     */
    public function isOverdue()
    {
        return $this->status == self::STATUS_ISSUED && $this->due_date < now();
    }

    /**
     * Calculate the fine amount.
     *
     * @param float $finePerDay
     * @return float
     */
    public function calculateFine($finePerDay = 1.0)
    {
        if ($this->status == self::STATUS_RETURNED) {
            if ($this->return_date > $this->due_date) {
                $daysLate = $this->return_date->diffInDays($this->due_date);
                return $daysLate * $finePerDay;
            }
        } elseif ($this->isOverdue()) {
            $daysLate = now()->diffInDays($this->due_date);
            return $daysLate * $finePerDay;
        }
        
        return 0;
    }
}
