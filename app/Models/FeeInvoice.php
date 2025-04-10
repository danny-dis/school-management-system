<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeInvoice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Status constants
     */
    const STATUS_UNPAID = 'unpaid';
    const STATUS_PARTIALLY_PAID = 'partially_paid';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'class_id',
        'section_id',
        'fee_type_id',
        'invoice_no',
        'amount',
        'discount',
        'fine',
        'total',
        'paid',
        'due',
        'issue_date',
        'due_date',
        'status',
        'description',
        'academic_year_id',
        'created_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'float',
        'discount' => 'float',
        'fine' => 'float',
        'total' => 'float',
        'paid' => 'float',
        'due' => 'float',
        'issue_date' => 'date',
        'due_date' => 'date',
    ];

    /**
     * Get the student that owns the invoice.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the class that owns the invoice.
     */
    public function class()
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    /**
     * Get the section that owns the invoice.
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the fee type that owns the invoice.
     */
    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

    /**
     * Get the academic year that owns the invoice.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the payments for the invoice.
     */
    public function payments()
    {
        return $this->hasMany(FeePayment::class, 'invoice_id');
    }

    /**
     * Get the user who created the invoice.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include unpaid invoices.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', self::STATUS_UNPAID);
    }

    /**
     * Scope a query to only include partially paid invoices.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePartiallyPaid($query)
    {
        return $query->where('status', self::STATUS_PARTIALLY_PAID);
    }

    /**
     * Scope a query to only include paid invoices.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope a query to only include overdue invoices.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverdue($query)
    {
        return $query->whereIn('status', [self::STATUS_UNPAID, self::STATUS_PARTIALLY_PAID])
            ->where('due_date', '<', now());
    }
}
