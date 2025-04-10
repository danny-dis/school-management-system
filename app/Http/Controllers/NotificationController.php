<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * Create a new controller instance.
     *
     * @param NotificationService $notificationService
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the notifications.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $notifications = $this->notificationService->getAllNotifications(Auth::id());
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            return redirect()->route('notifications.index')
                ->with('error', 'You are not authorized to perform this action.');
        }
        
        $this->notificationService->markAsRead($id);
        
        // If the notification has a link, redirect to it
        if ($notification->link) {
            return redirect($notification->link);
        }
        
        return redirect()->route('notifications.index')
            ->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(Auth::id());
        
        return redirect()->route('notifications.index')
            ->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            return redirect()->route('notifications.index')
                ->with('error', 'You are not authorized to perform this action.');
        }
        
        $this->notificationService->deleteNotification($id);
        
        return redirect()->route('notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }

    /**
     * Delete all notifications.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyAll()
    {
        $this->notificationService->deleteAllNotifications(Auth::id());
        
        return redirect()->route('notifications.index')
            ->with('success', 'All notifications deleted successfully.');
    }

    /**
     * Get unread notifications for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadNotifications()
    {
        $notifications = $this->notificationService->getUnreadNotifications(Auth::id());
        $count = $this->notificationService->countUnreadNotifications(Auth::id());
        
        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'count' => $count
        ]);
    }
}
