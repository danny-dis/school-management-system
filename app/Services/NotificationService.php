<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class NotificationService
{
    /**
     * Create a notification for a user
     *
     * @param int $userId
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @param string $type
     * @return Notification|null
     */
    public function createNotification($userId, $title, $message, $link = null, $type = 'info')
    {
        try {
            return Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'read' => false,
                'type' => $type
            ]);
        } catch (Exception $e) {
            Log::error('Error creating notification: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Create notifications for multiple users
     *
     * @param array $userIds
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @param string $type
     * @return int Number of notifications created
     */
    public function createNotifications(array $userIds, $title, $message, $link = null, $type = 'info')
    {
        try {
            $count = 0;
            $now = now();
            
            $notifications = [];
            foreach ($userIds as $userId) {
                $notifications[] = [
                    'user_id' => $userId,
                    'title' => $title,
                    'message' => $message,
                    'link' => $link,
                    'read' => false,
                    'type' => $type,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
                $count++;
            }
            
            if (!empty($notifications)) {
                Notification::insert($notifications);
            }
            
            return $count;
        } catch (Exception $e) {
            Log::error('Error creating multiple notifications: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return 0;
        }
    }

    /**
     * Create notifications for users with specific role
     *
     * @param string $role
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @param string $type
     * @return int Number of notifications created
     */
    public function notifyRole($role, $title, $message, $link = null, $type = 'info')
    {
        try {
            $users = User::role($role)->where('status', true)->get();
            $userIds = $users->pluck('id')->toArray();
            
            return $this->createNotifications($userIds, $title, $message, $link, $type);
        } catch (Exception $e) {
            Log::error('Error notifying role: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return 0;
        }
    }

    /**
     * Mark a notification as read
     *
     * @param int $notificationId
     * @return bool
     */
    public function markAsRead($notificationId)
    {
        try {
            $notification = Notification::find($notificationId);
            
            if (!$notification) {
                return false;
            }
            
            $notification->read = true;
            $notification->save();
            
            return true;
        } catch (Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user
     *
     * @param int $userId
     * @return int Number of notifications marked as read
     */
    public function markAllAsRead($userId)
    {
        try {
            return Notification::where('user_id', $userId)
                ->where('read', false)
                ->update(['read' => true]);
        } catch (Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return 0;
        }
    }

    /**
     * Delete a notification
     *
     * @param int $notificationId
     * @return bool
     */
    public function deleteNotification($notificationId)
    {
        try {
            $notification = Notification::find($notificationId);
            
            if (!$notification) {
                return false;
            }
            
            $notification->delete();
            
            return true;
        } catch (Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Delete all notifications for a user
     *
     * @param int $userId
     * @return int Number of notifications deleted
     */
    public function deleteAllNotifications($userId)
    {
        try {
            return Notification::where('user_id', $userId)->delete();
        } catch (Exception $e) {
            Log::error('Error deleting all notifications: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return 0;
        }
    }

    /**
     * Get unread notifications for a user
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnreadNotifications($userId, $limit = 10)
    {
        try {
            return Notification::where('user_id', $userId)
                ->where('read', false)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        } catch (Exception $e) {
            Log::error('Error getting unread notifications: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return collect();
        }
    }

    /**
     * Get all notifications for a user
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllNotifications($userId, $limit = 50)
    {
        try {
            return Notification::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        } catch (Exception $e) {
            Log::error('Error getting all notifications: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return collect();
        }
    }

    /**
     * Count unread notifications for a user
     *
     * @param int $userId
     * @return int
     */
    public function countUnreadNotifications($userId)
    {
        try {
            return Notification::where('user_id', $userId)
                ->where('read', false)
                ->count();
        } catch (Exception $e) {
            Log::error('Error counting unread notifications: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return 0;
        }
    }
}
