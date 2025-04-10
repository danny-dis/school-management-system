<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FeePayment
 * 
 * This model represents a fee payment in the fee management system.
 * 
 * @package App
 */
class FeePayment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fee_invoice_id', 
        'payment_method', 
        'amount', 
        'transaction_id', 
        'payment_date', 
        'status',
        'notes',
        'created_by'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'payment_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Payment method constants
     */
    const METHOD_CASH = 'cash';
    const METHOD_CHEQUE = 'cheque';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_ONLINE = 'online';
    const METHOD_OTHER = 'other';

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Get the invoice that owns the payment.
     */
    public function invoice()
    {
        return $this->belongsTo('App\FeeInvoice', 'fee_invoice_id');
    }

    /**
     * Get the user who created the payment.
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Get the payment method as text.
     *
     * @return string
     */
    public function getPaymentMethodTextAttribute()
    {
        switch ($this->payment_method) {
            case self::METHOD_CASH:
                return 'Cash';
            case self::METHOD_CHEQUE:
                return 'Cheque';
            case self::METHOD_BANK_TRANSFER:
                return 'Bank Transfer';
            case self::METHOD_ONLINE:
                return 'Online Payment';
            case self::METHOD_OTHER:
                return 'Other';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get the status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                return 'Pending';
            case self::STATUS_COMPLETED:
                return 'Completed';
            case self::STATUS_FAILED:
                return 'Failed';
            case self::STATUS_REFUNDED:
                return 'Refunded';
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
            case self::STATUS_PENDING:
                return 'warning';
            case self::STATUS_COMPLETED:
                return 'success';
            case self::STATUS_FAILED:
                return 'danger';
            case self::STATUS_REFUNDED:
                return 'info';
            default:
                return 'default';
        }
    }
}
