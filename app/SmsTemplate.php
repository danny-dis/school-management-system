<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\AppHelper;

/**
 * Class SmsTemplate
 * 
 * This model represents an SMS template in the communication system.
 * 
 * @package App
 */
class SmsTemplate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'body', 
        'variables',
        'status'
    ];

    /**
     * Get the template status as text.
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return $this->status == AppHelper::ACTIVE ? 'Active' : 'Inactive';
    }

    /**
     * Get the variables as an array.
     *
     * @return array
     */
    public function getVariablesArrayAttribute()
    {
        return json_decode($this->variables, true) ?? [];
    }

    /**
     * Set the variables from an array.
     *
     * @param  array  $value
     * @return void
     */
    public function setVariablesArrayAttribute($value)
    {
        $this->attributes['variables'] = json_encode($value);
    }

    /**
     * Parse the template with the given data.
     *
     * @param  array  $data
     * @return string
     */
    public function parse($data)
    {
        $body = $this->body;
        
        foreach ($data as $key => $value) {
            $body = str_replace('{'.$key.'}', $value, $body);
        }
        
        return $body;
    }
}
