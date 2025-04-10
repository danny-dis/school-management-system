<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'recipient',
        'subject',
        'message',
        'status',
        'response',
        'created_by'
    ];

    /**
     * Get the user who created the email log.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
