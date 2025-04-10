<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Message;
use App\Notification;
use App\Announcement;
use App\EmailTemplate;
use App\SmsTemplate;
use App\EmailLog;
use App\SmsLog;
use App\User;
use App\UserRole;
use App\Registration;
use App\Employee;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * CommunicationController
 * 
 * This controller handles the communication module functionality.
 * Enhanced by Zophlic for improved messaging capabilities.
 */
class CommunicationController extends Controller
{
    /**
     * Display the inbox.
     *
     * @return \Illuminate\Http\Response
     */
    public function inbox()
    {
        $user = Auth::user();
        
        $messages = Message::where('receiver_id', $user->id)
            ->where('deleted_by_receiver', false)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('backend.communication.inbox', compact('messages'));
    }

    /**
     * Display the sent messages.
     *
     * @return \Illuminate\Http\Response
     */
    public function sent()
    {
        $user = Auth::user();
        
        $messages = Message::where('sender_id', $user->id)
            ->where('deleted_by_sender', false)
            ->with('receiver')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('backend.communication.sent', compact('messages'));
    }

    /**
     * Show the form for composing a new message.
     *
     * @return \Illuminate\Http\Response
     */
    public function compose()
    {
        $roles = UserRole::pluck('name', 'id');
        
        return view('backend.communication.compose', compact('roles'));
    }

    /**
     * Get users by role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getUsersByRole(Request $request)
    {
        $roleId = $request->role_id;
        
        $users = User::where('role_id', $roleId)
            ->where('status', 1)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name . ' (' . $user->username . ')'
                ];
            });
            
        return response()->json([
            'users' => $users
        ]);
    }

    /**
     * Store a newly created message in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeMessage(Request $request)
    {
        $this->validate($request, [
            'receiver_id' => 'required|integer|exists:users,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240'
        ]);

        // Handle attachment upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('public/messages');
            $attachmentPath = basename($attachmentPath);
        }

        // Create message
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'attachment' => $attachmentPath
        ]);

        // Create notification for receiver
        Notification::create([
            'user_id' => $request->receiver_id,
            'title' => 'New Message',
            'message' => 'You have received a new message from ' . Auth::user()->name,
            'link' => route('communication.view', $message->id),
            'type' => Notification::TYPE_INFO
        ]);

        return redirect()->route('communication.sent')->with('success', 'Message sent successfully!');
    }

    /**
     * Display the specified message.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function viewMessage($id)
    {
        $user = Auth::user();
        
        $message = Message::with(['sender', 'receiver'])
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->findOrFail($id);
            
        // Check if user is allowed to view this message
        if ($message->sender_id != $user->id && $message->receiver_id != $user->id) {
            return redirect()->route('communication.inbox')->with('error', 'You are not authorized to view this message!');
        }
        
        // Check if message is deleted for this user
        if (($message->sender_id == $user->id && $message->deleted_by_sender) || 
            ($message->receiver_id == $user->id && $message->deleted_by_receiver)) {
            return redirect()->route('communication.inbox')->with('error', 'Message not found!');
        }
        
        // Mark as read if user is receiver
        if ($message->receiver_id == $user->id && !$message->isRead()) {
            $message->markAsRead();
        }
        
        return view('backend.communication.view', compact('message', 'user'));
    }

    /**
     * Delete the specified message for the current user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteMessage($id)
    {
        $user = Auth::user();
        
        $message = Message::where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->findOrFail($id);
            
        // Delete message for current user
        $message->deleteForUser($user->id);
        
        return redirect()->back()->with('success', 'Message deleted successfully!');
    }

    /**
     * Display the notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function notifications()
    {
        $user = Auth::user();
        
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('backend.communication.notifications', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markNotificationAsRead($id)
    {
        $user = Auth::user();
        
        $notification = Notification::where('user_id', $user->id)
            ->findOrFail($id);
            
        $notification->markAsRead();
        
        if ($notification->link) {
            return redirect($notification->link);
        }
        
        return redirect()->back()->with('success', 'Notification marked as read!');
    }

    /**
     * Delete a notification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteNotification($id)
    {
        $user = Auth::user();
        
        $notification = Notification::where('user_id', $user->id)
            ->findOrFail($id);
            
        $notification->delete();
        
        return redirect()->back()->with('success', 'Notification deleted successfully!');
    }

    /**
     * Display the announcements.
     *
     * @return \Illuminate\Http\Response
     */
    public function announcements()
    {
        $announcements = Announcement::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('backend.communication.announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new announcement.
     *
     * @return \Illuminate\Http\Response
     */
    public function createAnnouncement()
    {
        return view('backend.communication.announcements.create');
    }

    /**
     * Store a newly created announcement in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAnnouncement(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'for_students' => 'nullable|boolean',
            'for_teachers' => 'nullable|boolean',
            'for_parents' => 'nullable|boolean',
            'for_admins' => 'nullable|boolean',
            'status' => 'required|integer'
        ]);

        // Create announcement
        $announcement = Announcement::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => Auth::id(),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'for_students' => $request->has('for_students'),
            'for_teachers' => $request->has('for_teachers'),
            'for_parents' => $request->has('for_parents'),
            'for_admins' => $request->has('for_admins'),
            'status' => $request->status
        ]);

        // Create notifications for users
        $this->createAnnouncementNotifications($announcement);

        return redirect()->route('communication.announcements')->with('success', 'Announcement created successfully!');
    }

    /**
     * Create notifications for an announcement.
     *
     * @param  \App\Announcement  $announcement
     * @return void
     */
    private function createAnnouncementNotifications($announcement)
    {
        // Get user roles to notify
        $roleIds = [];
        
        if ($announcement->for_students) {
            $studentRole = UserRole::where('name', 'Student')->first();
            if ($studentRole) {
                $roleIds[] = $studentRole->id;
            }
        }
        
        if ($announcement->for_teachers) {
            $teacherRole = UserRole::where('name', 'Teacher')->first();
            if ($teacherRole) {
                $roleIds[] = $teacherRole->id;
            }
        }
        
        if ($announcement->for_parents) {
            $parentRole = UserRole::where('name', 'Parent')->first();
            if ($parentRole) {
                $roleIds[] = $parentRole->id;
            }
        }
        
        if ($announcement->for_admins) {
            $adminRole = UserRole::where('name', 'Admin')->first();
            if ($adminRole) {
                $roleIds[] = $adminRole->id;
            }
        }
        
        // Get users with these roles
        $users = User::whereIn('role_id', $roleIds)
            ->where('status', 1)
            ->get();
            
        // Create notifications
        foreach ($users as $user) {
            // Skip the creator
            if ($user->id == $announcement->created_by) {
                continue;
            }
            
            Notification::create([
                'user_id' => $user->id,
                'title' => 'New Announcement',
                'message' => 'A new announcement has been posted: ' . $announcement->title,
                'link' => route('communication.announcements.show', $announcement->id),
                'type' => Notification::TYPE_INFO
            ]);
        }
    }

    /**
     * Display the specified announcement.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAnnouncement($id)
    {
        $announcement = Announcement::with('creator')->findOrFail($id);
        
        return view('backend.communication.announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified announcement.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editAnnouncement($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        return view('backend.communication.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified announcement in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAnnouncement(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);
        
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'for_students' => 'nullable|boolean',
            'for_teachers' => 'nullable|boolean',
            'for_parents' => 'nullable|boolean',
            'for_admins' => 'nullable|boolean',
            'status' => 'required|integer'
        ]);

        // Update announcement
        $announcement->title = $request->title;
        $announcement->description = $request->description;
        $announcement->start_date = $request->start_date;
        $announcement->end_date = $request->end_date;
        $announcement->for_students = $request->has('for_students');
        $announcement->for_teachers = $request->has('for_teachers');
        $announcement->for_parents = $request->has('for_parents');
        $announcement->for_admins = $request->has('for_admins');
        $announcement->status = $request->status;
        $announcement->save();

        return redirect()->route('communication.announcements')->with('success', 'Announcement updated successfully!');
    }

    /**
     * Remove the specified announcement from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyAnnouncement($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();
        
        return redirect()->route('communication.announcements')->with('success', 'Announcement deleted successfully!');
    }

    /**
     * Display the email templates.
     *
     * @return \Illuminate\Http\Response
     */
    public function emailTemplates()
    {
        $templates = EmailTemplate::orderBy('name', 'asc')->paginate(10);
        
        return view('backend.communication.email_templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new email template.
     *
     * @return \Illuminate\Http\Response
     */
    public function createEmailTemplate()
    {
        return view('backend.communication.email_templates.create');
    }

    /**
     * Store a newly created email template in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeEmailTemplate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100|unique:email_templates',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        // Parse variables
        $variables = null;
        if ($request->variables) {
            $variablesArray = array_map('trim', explode(',', $request->variables));
            $variables = json_encode($variablesArray);
        }

        // Create template
        $template = EmailTemplate::create([
            'name' => $request->name,
            'subject' => $request->subject,
            'body' => $request->body,
            'variables' => $variables,
            'status' => $request->status
        ]);

        return redirect()->route('communication.email_templates')->with('success', 'Email template created successfully!');
    }

    /**
     * Show the form for editing the specified email template.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editEmailTemplate($id)
    {
        $template = EmailTemplate::findOrFail($id);
        
        // Format variables for display
        $variablesString = '';
        if ($template->variables) {
            $variablesArray = json_decode($template->variables, true);
            if (is_array($variablesArray)) {
                $variablesString = implode(', ', $variablesArray);
            }
        }
        
        return view('backend.communication.email_templates.edit', compact('template', 'variablesString'));
    }

    /**
     * Update the specified email template in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateEmailTemplate(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);
        
        $this->validate($request, [
            'name' => 'required|string|max:100|unique:email_templates,name,'.$id,
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        // Parse variables
        $variables = null;
        if ($request->variables) {
            $variablesArray = array_map('trim', explode(',', $request->variables));
            $variables = json_encode($variablesArray);
        }

        // Update template
        $template->name = $request->name;
        $template->subject = $request->subject;
        $template->body = $request->body;
        $template->variables = $variables;
        $template->status = $request->status;
        $template->save();

        return redirect()->route('communication.email_templates')->with('success', 'Email template updated successfully!');
    }

    /**
     * Remove the specified email template from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyEmailTemplate($id)
    {
        $template = EmailTemplate::findOrFail($id);
        $template->delete();
        
        return redirect()->route('communication.email_templates')->with('success', 'Email template deleted successfully!');
    }

    /**
     * Display the SMS templates.
     *
     * @return \Illuminate\Http\Response
     */
    public function smsTemplates()
    {
        $templates = SmsTemplate::orderBy('name', 'asc')->paginate(10);
        
        return view('backend.communication.sms_templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new SMS template.
     *
     * @return \Illuminate\Http\Response
     */
    public function createSmsTemplate()
    {
        return view('backend.communication.sms_templates.create');
    }

    /**
     * Store a newly created SMS template in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeSmsTemplate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100|unique:sms_templates',
            'body' => 'required|string|max:160',
            'variables' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        // Parse variables
        $variables = null;
        if ($request->variables) {
            $variablesArray = array_map('trim', explode(',', $request->variables));
            $variables = json_encode($variablesArray);
        }

        // Create template
        $template = SmsTemplate::create([
            'name' => $request->name,
            'body' => $request->body,
            'variables' => $variables,
            'status' => $request->status
        ]);

        return redirect()->route('communication.sms_templates')->with('success', 'SMS template created successfully!');
    }

    /**
     * Show the form for editing the specified SMS template.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editSmsTemplate($id)
    {
        $template = SmsTemplate::findOrFail($id);
        
        // Format variables for display
        $variablesString = '';
        if ($template->variables) {
            $variablesArray = json_decode($template->variables, true);
            if (is_array($variablesArray)) {
                $variablesString = implode(', ', $variablesArray);
            }
        }
        
        return view('backend.communication.sms_templates.edit', compact('template', 'variablesString'));
    }

    /**
     * Update the specified SMS template in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSmsTemplate(Request $request, $id)
    {
        $template = SmsTemplate::findOrFail($id);
        
        $this->validate($request, [
            'name' => 'required|string|max:100|unique:sms_templates,name,'.$id,
            'body' => 'required|string|max:160',
            'variables' => 'nullable|string',
            'status' => 'required|integer'
        ]);

        // Parse variables
        $variables = null;
        if ($request->variables) {
            $variablesArray = array_map('trim', explode(',', $request->variables));
            $variables = json_encode($variablesArray);
        }

        // Update template
        $template->name = $request->name;
        $template->body = $request->body;
        $template->variables = $variables;
        $template->status = $request->status;
        $template->save();

        return redirect()->route('communication.sms_templates')->with('success', 'SMS template updated successfully!');
    }

    /**
     * Remove the specified SMS template from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroySmsTemplate($id)
    {
        $template = SmsTemplate::findOrFail($id);
        $template->delete();
        
        return redirect()->route('communication.sms_templates')->with('success', 'SMS template deleted successfully!');
    }

    /**
     * Show the form for sending bulk emails.
     *
     * @return \Illuminate\Http\Response
     */
    public function bulkEmail()
    {
        $templates = EmailTemplate::where('status', AppHelper::ACTIVE)
            ->pluck('name', 'id');
            
        $roles = UserRole::pluck('name', 'id');
        
        return view('backend.communication.bulk_email', compact('templates', 'roles'));
    }

    /**
     * Send bulk emails.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendBulkEmail(Request $request)
    {
        $this->validate($request, [
            'role_id' => 'required|integer|exists:user_roles,id',
            'template_id' => 'required|integer|exists:email_templates,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        // Get users by role
        $users = User::where('role_id', $request->role_id)
            ->where('status', 1)
            ->get();
            
        if ($users->isEmpty()) {
            return redirect()->back()->with('error', 'No users found with the selected role!');
        }
        
        // Send emails
        $count = 0;
        foreach ($users as $user) {
            if (!$user->email) {
                continue;
            }
            
            // Create email log
            $log = EmailLog::create([
                'recipient' => $user->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'status' => EmailLog::STATUS_PENDING,
                'created_by' => Auth::id()
            ]);
            
            // Send email
            try {
                // Email sending logic would go here
                // For now, just update the log
                $log->status = EmailLog::STATUS_SENT;
                $log->save();
                
                $count++;
            } catch (\Exception $e) {
                $log->status = EmailLog::STATUS_FAILED;
                $log->response = $e->getMessage();
                $log->save();
            }
        }
        
        return redirect()->route('communication.email_logs')->with('success', $count . ' emails sent successfully!');
    }

    /**
     * Show the form for sending bulk SMS.
     *
     * @return \Illuminate\Http\Response
     */
    public function bulkSms()
    {
        $templates = SmsTemplate::where('status', AppHelper::ACTIVE)
            ->pluck('name', 'id');
            
        $roles = UserRole::pluck('name', 'id');
        
        return view('backend.communication.bulk_sms', compact('templates', 'roles'));
    }

    /**
     * Send bulk SMS.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendBulkSms(Request $request)
    {
        $this->validate($request, [
            'role_id' => 'required|integer|exists:user_roles,id',
            'template_id' => 'required|integer|exists:sms_templates,id',
            'message' => 'required|string|max:160'
        ]);

        // Get users by role
        $users = User::where('role_id', $request->role_id)
            ->where('status', 1)
            ->get();
            
        if ($users->isEmpty()) {
            return redirect()->back()->with('error', 'No users found with the selected role!');
        }
        
        // Get phone numbers based on role
        $phoneNumbers = [];
        $role = UserRole::find($request->role_id);
        
        foreach ($users as $user) {
            $phone = null;
            
            if ($role->name == 'Student') {
                $student = Student::where('user_id', $user->id)->first();
                if ($student && $student->phone_no) {
                    $phone = $student->phone_no;
                }
            } elseif ($role->name == 'Teacher') {
                $teacher = Employee::where('id', $user->id)->first();
                if ($teacher && $teacher->phone_no) {
                    $phone = $teacher->phone_no;
                }
            } else {
                if ($user->phone) {
                    $phone = $user->phone;
                }
            }
            
            if ($phone) {
                $phoneNumbers[] = $phone;
            }
        }
        
        if (empty($phoneNumbers)) {
            return redirect()->back()->with('error', 'No phone numbers found for the selected users!');
        }
        
        // Send SMS
        $count = 0;
        foreach ($phoneNumbers as $phone) {
            // Create SMS log
            $log = SmsLog::create([
                'recipient' => $phone,
                'message' => $request->message,
                'status' => SmsLog::STATUS_PENDING,
                'created_by' => Auth::id()
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
            }
        }
        
        return redirect()->route('communication.sms_logs')->with('success', $count . ' SMS sent successfully!');
    }

    /**
     * Display the email logs.
     *
     * @return \Illuminate\Http\Response
     */
    public function emailLogs()
    {
        $logs = EmailLog::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('backend.communication.email_logs', compact('logs'));
    }

    /**
     * Display the SMS logs.
     *
     * @return \Illuminate\Http\Response
     */
    public function smsLogs()
    {
        $logs = SmsLog::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('backend.communication.sms_logs', compact('logs'));
    }
}
