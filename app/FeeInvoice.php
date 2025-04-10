<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FeeInvoice
 * 
 * This model represents a fee invoice in the fee management system.
 * 
 * @package App
 */
class FeeInvoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_no', 
        'student_id', 
        'class_id', 
        'section_id', 
        'fee_type_id', 
        'amount',
        'discount',
        'fine',
        'total_amount',
        'paid_amount',
        'due_amount',
        'issue_date',
        'due_date',
        'status',
        'academic_year_id',
        'notes'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'issue_date',
        'due_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Status constants
     */
    const STATUS_UNPAID = 'unpaid';
    const STATUS_PARTIALLY_PAID = 'partially_paid';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the student that owns the invoice.
     */
    public function student()
    {
        return $this->belongsTo('App\Registration', 'student_id');
    }

    /**
     * Get the class that owns the invoice.
     */
    public function class()
    {
        return $this->belongsTo('App\IClass');
    }

    /**
     * Get the section that owns the invoice.
     */
    public function section()
    {
        return $this->belongsTo('App\Section');
    }

    /**
     * Get the fee type that owns the invoice.
     */
    public function feeType()
    {
        return $this->belongsTo('App\FeeType');
    }

    /**
     * Get the academic year that owns the invoice.
     */
    public function academicYear()
    {
        return $this->belongsTo('App\AcademicYear');
    }

    /**
     * Get the payments for the invoice.
     */
    public function payments()
    {
        return $this->hasMany('App\FeePayment');
    }

    /**
     * Get the status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case self::STATUS_UNPAID:
                return 'Unpaid';
            case self::STATUS_PARTIALLY_PAID:
                return 'Partially Paid';
            case self::STATUS_PAID:
                return 'Paid';
            case self::STATUS_CANCELLED:
                return 'Cancelled';
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
            case self::STATUS_UNPAID:
                return 'danger';
            case self::STATUS_PARTIALLY_PAID:
                return 'warning';
            case self::STATUS_PAID:
                return 'success';
            case self::STATUS_CANCELLED:
                return 'default';
            default:
                return 'info';
        }
    }

    /**
     * Check if the invoice is overdue.
     *
     * @return bool
     */
    public function isOverdue()
    {
        return $this->status != self::STATUS_PAID && $this->status != self::STATUS_CANCELLED && $this->due_date < now();
    }
}
