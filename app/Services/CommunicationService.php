<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Announcement;
use App\Models\EmailLog;
use App\Models\SmsLog;
use App\Models\EmailTemplate;
use App\Models\SmsTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Exception;

class CommunicationService
{
    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * CommunicationService constructor.
     *
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Send a message
     *
     * @param int $senderId
     * @param int $receiverId
     * @param string $subject
     * @param string $message
     * @param string|null $attachment
     * @return Message|null
     */
    public function sendMessage($senderId, $receiverId, $subject, $message, $attachment = null)
    {
        try {
            DB::beginTransaction();
            
            $messageObj = Message::create([
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'subject' => $subject,
                'message' => $message,
                'attachment' => $attachment
            ]);
            
            // Create notification for receiver
            $this->notificationService->createNotification(
                $receiverId,
                'New Message',
                'You have received a new message: ' . $subject,
                route('communication.view', $messageObj->id),
                'info'
            );
            
            DB::commit();
            return $messageObj;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error sending message: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Mark a message as read
     *
     * @param int $messageId
     * @param int $userId
     * @return bool
     */
    public function markMessageAsRead($messageId, $userId)
    {
        try {
            $message = Message::find($messageId);
            
            if (!$message || $message->receiver_id != $userId) {
                return false;
            }
            
            $message->markAsRead();
            return true;
        } catch (Exception $e) {
            Log::error('Error marking message as read: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Delete a message for a user
     *
     * @param int $messageId
     * @param int $userId
     * @return bool
     */
    public function deleteMessage($messageId, $userId)
    {
        try {
            $message = Message::find($messageId);
            
            if (!$message || ($message->sender_id != $userId && $message->receiver_id != $userId)) {
                return false;
            }
            
            $message->deleteForUser($userId);
            return true;
        } catch (Exception $e) {
            Log::error('Error deleting message: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Get inbox messages for a user
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getInboxMessages($userId, $perPage = 15)
    {
        return Message::with('sender')
            ->where('receiver_id', $userId)
            ->where('deleted_by_receiver', false)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get sent messages for a user
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getSentMessages($userId, $perPage = 15)
    {
        return Message::with('receiver')
            ->where('sender_id', $userId)
            ->where('deleted_by_sender', false)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Create an announcement
     *
     * @param array $data
     * @param int $createdBy
     * @return Announcement|null
     */
    public function createAnnouncement(array $data, $createdBy)
    {
        try {
            DB::beginTransaction();
            
            $announcement = Announcement::create([
                'title' => $data['title'],
                'message' => $data['message'],
                'for_students' => $data['for_students'] ?? false,
                'for_teachers' => $data['for_teachers'] ?? false,
                'for_parents' => $data['for_parents'] ?? false,
                'for_admins' => $data['for_admins'] ?? false,
                'status' => $data['status'] ?? true,
                'created_by' => $createdBy
            ]);
            
            // Create notifications for users
            $this->notifyUsersAboutAnnouncement($announcement);
            
            DB::commit();
            return $announcement;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating announcement: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Update an announcement
     *
     * @param int $id
     * @param array $data
     * @return Announcement|null
     */
    public function updateAnnouncement($id, array $data)
    {
        try {
            $announcement = Announcement::find($id);
            
            if (!$announcement) {
                return null;
            }
            
            $announcement->update($data);
            return $announcement;
        } catch (Exception $e) {
            Log::error('Error updating announcement: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return null;
        }
    }

    /**
     * Delete an announcement
     *
     * @param int $id
     * @return bool
     */
    public function deleteAnnouncement($id)
    {
        try {
            $announcement = Announcement::find($id);
            
            if (!$announcement) {
                return false;
            }
            
            $announcement->delete();
            return true;
        } catch (Exception $e) {
            Log::error('Error deleting announcement: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    /**
     * Get announcements
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAnnouncements($perPage = 15)
    {
        return Announcement::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Send bulk email
     *
     * @param array $userIds
     * @param string $subject
     * @param string $message
     * @param int $sentBy
     * @return int Number of emails sent
     */
    public function sendBulkEmail(array $userIds, $subject, $message, $sentBy)
    {
        try {
            $count = 0;
            $users = \App\Models\User::whereIn('id', $userIds)->where('status', true)->get();
            
            foreach ($users as $user) {
                if (!$user->email) {
                    continue;
                }
                
                // Create email log
                $log = EmailLog::create([
                    'recipient' => $user->email,
                    'subject' => $subject,
                    'message' => $message,
                    'status' => EmailLog::STATUS_PENDING,
                    'created_by' => $sentBy
                ]);
                
                // Send email
                try {
                    Mail::to($user->email)->send(new \App\Mail\GenericEmail($subject, $message));
                    
                    $log->status = EmailLog::STATUS_SENT;
                    $log->save();
                    
                    $count++;
                } catch (\Exception $e) {
                    $log->status = EmailLog::STATUS_FAILED;
                    $log->response = $e->getMessage();
                    $log->save();
                    
                    Log::error('Error sending email to ' . $user->email . ': ' . $e->getMessage());
                }
            }
            
            return $count;
        } catch (Exception $e) {
            Log::error('Error sending bulk email: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return 0;
        }
    }

    /**
     * Send bulk SMS
     *
     * @param array $userIds
     * @param string $message
     * @param int $sentBy
     * @return int Number of SMS sent
     */
    public function sendBulkSms(array $userIds, $message, $sentBy)
    {
        try {
            $count = 0;
            $users = \App\Models\User::whereIn('id', $userIds)->where('status', true)->get();
            
            foreach ($users as $user) {
                if (!$user->phone_no) {
                    continue;
                }
                
                // Create SMS log
                $log = SmsLog::create([
                    'recipient' => $user->phone_no,
                    'message' => $message,
                    'status' => SmsLog::STATUS_PENDING,
                    'created_by' => $sentBy
                ]);
                
                // Send SMS
                try {
                    // SMS sending logic would go here
                    // For now, just update the log
                    $log->status = SmsLog::STATUS_SENT;
                    $log->save();
                    
                    $count++;
                } catch (\Exception $e) {
                    $log->status = SmsLog::STATUS_FAILED;
                    $log->response = $e->getMessage();
                    $log->save();
                    
                    Log::error('Error sending SMS to ' . $user->phone_no . ': ' . $e->getMessage());
                }
            }
            
            return $count;
        } catch (Exception $e) {
            Log::error('Error sending bulk SMS: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return 0;
        }
    }

    /**
     * Get email logs
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getEmailLogs($perPage = 15)
    {
        return EmailLog::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get SMS logs
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getSmsLogs($perPage = 15)
    {
        return SmsLog::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Notify users about an announcement
     *
     * @param Announcement $announcement
     * @return void
     */
    protected function notifyUsersAboutAnnouncement(Announcement $announcement)
    {
        $roleIds = [];
        
        if ($announcement->for_students) {
            $studentRole = \Spatie\Permission\Models\Role::where('name', 'Student')->first();
            if ($studentRole) {
                $roleIds[] = $studentRole->id;
            }
        }
        
        if ($announcement->for_teachers) {
            $teacherRole = \Spatie\Permission\Models\Role::where('name', 'Teacher')->first();
            if ($teacherRole) {
                $roleIds[] = $teacherRole->id;
            }
        }
        
        if ($announcement->for_parents) {
            $parentRole = \Spatie\Permission\Models\Role::where('name', 'Parent')->first();
            if ($parentRole) {
                $roleIds[] = $parentRole->id;
            }
        }
        
        if ($announcement->for_admins) {
            $adminRole = \Spatie\Permission\Models\Role::where('name', 'Admin')->first();
            if ($adminRole) {
                $roleIds[] = $adminRole->id;
            }
        }
        
        // Get users with these roles
        $users = \App\Models\User::whereHas('roles', function ($query) use ($roleIds) {
            $query->whereIn('id', $roleIds);
        })
        ->where('status', true)
        ->get();
        
        // Create notifications
        foreach ($users as $user) {
            // Skip the creator
            if ($user->id == $announcement->created_by) {
                continue;
            }
            
            $this->notificationService->createNotification(
                $user->id,
                'New Announcement',
                $announcement->title,
                route('communication.announcements.show', $announcement->id),
                'info'
            );
        }
    }
}
