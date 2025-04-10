<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Course;
use App\Lesson;
use App\LessonResource;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

/**
 * LessonController
 * 
 * This controller handles the lesson functionality for the online learning module.
 */
class LessonController extends Controller
{
    /**
     * Display a listing of lessons for a course.
     *
     * @param  int  $courseId
     * @return \Illuminate\Http\Response
     */
    public function index($courseId)
    {
        $course = Course::with(['lessons' => function($query) {
            $query->orderBy('order', 'asc');
        }])->findOrFail($courseId);
        
        return view('backend.online_learning.lessons.index', compact('course'));
    }

    /**
     * Show the form for creating a new lesson.
     *
     * @param  int  $courseId
     * @return \Illuminate\Http\Response
     */
    public function create($courseId)
    {
        $course = Course::findOrFail($courseId);
        
        return view('backend.online_learning.lessons.create', compact('course'));
    }

    /**
     * Store a newly created lesson in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);
        
        $this->validate($request, [
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'attachment' => 'nullable|file|max:10240',
            'order' => 'nullable|integer',
            'duration' => 'nullable|integer',
            'is_free' => 'nullable|boolean',
            'status' => 'required|integer'
        ]);

        // Handle attachment upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('public/lessons');
            $attachmentPath = basename($attachmentPath);
        }

        // Get the highest order if not provided
        $order = $request->order;
        if (!$order) {
            $order = Lesson::where('course_id', $courseId)->max('order') + 1;
        }

        // Create lesson
        $lesson = Lesson::create([
            'title' => $request->title,
            'description' => $request->description,
            'course_id' => $courseId,
            'content' => $request->content,
            'video_url' => $request->video_url,
            'attachment' => $attachmentPath,
            'order' => $order,
            'duration' => $request->duration,
            'is_free' => $request->has('is_free'),
            'status' => $request->status
        ]);

        return redirect()->route('online_learning.lessons.index', $courseId)
            ->with('success', 'Lesson created successfully!');
    }

    /**
     * Display the specified lesson.
     *
     * @param  int  $courseId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($courseId, $id)
    {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::with('resources')->findOrFail($id);
        
        return view('backend.online_learning.lessons.show', compact('course', 'lesson'));
    }

    /**
     * Show the form for editing the specified lesson.
     *
     * @param  int  $courseId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($courseId, $id)
    {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($id);
        
        return view('backend.online_learning.lessons.edit', compact('course', 'lesson'));
    }

    /**
     * Update the specified lesson in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $courseId, $id)
    {
        $course = Course::findOrFail($courseId);
        $lesson = Lesson::findOrFail($id);
        
        $this->validate($request, [
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'attachment' => 'nullable|file|max:10240',
            'order' => 'nullable|integer',
            'duration' => 'nullable|integer',
            'is_free' => 'nullable|boolean',
            'status' => 'required|integer'
        ]);

        // Handle attachment upload
        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($lesson->attachment) {
                Storage::delete('public/lessons/'.$lesson->attachment);
            }
            
            $attachmentPath = $request->file('attachment')->store('public/lessons');
            $lesson->attachment = basename($attachmentPath);
        }

        // Update lesson
        $lesson->title = $request->title;
        $lesson->description = $request->description;
        $lesson->content = $request->content;
        $lesson->video_url = $request->video_url;
        $lesson->order = $request->order;
        $lesson->duration = $request->duration;
        $lesson->is_free = $request->has('is_free');
        $lesson->status = $request->status;
        $lesson->save();

        return redirect()->route('online_learning.lessons.index', $courseId)
            ->with('success', 'Lesson updated successfully!');
    }

    /**
     * Remove the specified lesson from storage.
     *
     * @param  int  $courseId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($courseId, $id)
    {
        $lesson = Lesson::findOrFail($id);
        
        // Delete attachment if exists
        if ($lesson->attachment) {
            Storage::delete('public/lessons/'.$lesson->attachment);
        }
        
        $lesson->delete();
        
        return redirect()->route('online_learning.lessons.index', $courseId)
            ->with('success', 'Lesson deleted successfully!');
    }

    /**
     * Reorder lessons.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @return \Illuminate\Http\Response
     */
    public function reorder(Request $request, $courseId)
    {
        $this->validate($request, [
            'lessons' => 'required|array',
            'lessons.*' => 'integer|exists:lessons,id'
        ]);
        
        $lessons = $request->lessons;
        
        foreach ($lessons as $order => $lessonId) {
            Lesson::where('id', $lessonId)->update(['order' => $order + 1]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Add a resource to a lesson.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $courseId
     * @param  int  $lessonId
     * @return \Illuminate\Http\Response
     */
    public function addResource(Request $request, $courseId, $lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);
        
        $this->validate($request, [
            'title' => 'required|string|max:100',
            'type' => 'required|string|in:document,video,audio,link,image,other',
            'file' => 'nullable|file|max:10240',
            'external_url' => 'nullable|url',
            'description' => 'nullable|string'
        ]);

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('public/resources');
            $filePath = basename($filePath);
        }

        // Create resource
        $resource = LessonResource::create([
            'lesson_id' => $lessonId,
            'title' => $request->title,
            'type' => $request->type,
            'file_path' => $filePath,
            'external_url' => $request->external_url,
            'description' => $request->description
        ]);

        return redirect()->route('online_learning.lessons.show', [$courseId, $lessonId])
            ->with('success', 'Resource added successfully!');
    }

    /**
     * Remove a resource from a lesson.
     *
     * @param  int  $courseId
     * @param  int  $lessonId
     * @param  int  $resourceId
     * @return \Illuminate\Http\Response
     */
    public function removeResource($courseId, $lessonId, $resourceId)
    {
        $resource = LessonResource::findOrFail($resourceId);
        
        // Delete file if exists
        if ($resource->file_path) {
            Storage::delete('public/resources/'.$resource->file_path);
        }
        
        $resource->delete();
        
        return redirect()->route('online_learning.lessons.show', [$courseId, $lessonId])
            ->with('success', 'Resource removed successfully!');
    }
}
