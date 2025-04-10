<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\AppHelper;

/**
 * Class BookCategory
 * 
 * This model represents a book category in the library management system.
 * 
 * @package App
 */
class BookCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'description', 
        'status'
    ];

    /**
     * Get the books for the category.
     */
    public function books()
    {
        return $this->hasMany('App\Book', 'category_id');
    }

    /**
     * Get the category status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->status == AppHelper::ACTIVE ? 'Active' : 'Inactive';
    }
}
