<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\AppHelper;

/**
 * Class Book
 * 
 * This model represents a book in the library management system.
 * 
 * @package App
 */
class Book extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 
        'isbn', 
        'author', 
        'publisher', 
        'edition', 
        'category_id',
        'description',
        'publish_year',
        'quantity',
        'available',
        'rack_no',
        'image',
        'status'
    ];

    /**
     * Get the category that owns the book.
     */
    public function category()
    {
        return $this->belongsTo('App\BookCategory');
    }

    /**
     * Get the issues for the book.
     */
    public function issues()
    {
        return $this->hasMany('App\BookIssue');
    }

    /**
     * Get the book status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->status == AppHelper::ACTIVE ? 'Active' : 'Inactive';
    }

    /**
     * Check if the book is available for issue.
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->available > 0 && $this->status == AppHelper::ACTIVE;
    }
}
