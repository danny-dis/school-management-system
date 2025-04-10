<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LessonResource
 * 
 * This model represents a resource attached to a lesson.
 * 
 * @package App
 */
class LessonResource extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lesson_id', 
        'title', 
        'type', 
        'file_path', 
        'external_url', 
        'description'
    ];

    /**
     * Resource types
     */
    const TYPE_DOCUMENT = 'document';
    const TYPE_VIDEO = 'video';
    const TYPE_AUDIO = 'audio';
    const TYPE_LINK = 'link';
    const TYPE_IMAGE = 'image';
    const TYPE_OTHER = 'other';

    /**
     * Get the lesson that owns the resource.
     */
    public function lesson()
    {
        return $this->belongsTo('App\Lesson');
    }

    /**
     * Get the resource type as text.
     *
     * @return string
     */
    public function getTypeTextAttribute()
    {
        switch ($this->type) {
            case self::TYPE_DOCUMENT:
                return 'Document';
            case self::TYPE_VIDEO:
                return 'Video';
            case self::TYPE_AUDIO:
                return 'Audio';
            case self::TYPE_LINK:
                return 'External Link';
            case self::TYPE_IMAGE:
                return 'Image';
            case self::TYPE_OTHER:
                return 'Other';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get the resource icon class.
     *
     * @return string
     */
    public function getIconClassAttribute()
    {
        switch ($this->type) {
            case self::TYPE_DOCUMENT:
                return 'fa fa-file-pdf-o';
            case self::TYPE_VIDEO:
                return 'fa fa-file-video-o';
            case self::TYPE_AUDIO:
                return 'fa fa-file-audio-o';
            case self::TYPE_LINK:
                return 'fa fa-external-link';
            case self::TYPE_IMAGE:
                return 'fa fa-file-image-o';
            case self::TYPE_OTHER:
                return 'fa fa-file-o';
            default:
                return 'fa fa-file';
        }
    }
}
