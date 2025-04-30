<?php

namespace Modules\Document\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Modules\Document\Events\NotificationReceived;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth('tenant')->user()->notifications()->paginate(10);
        return view('document::notifications.index', compact('notifications'));
    }

    public function getUnreadCount()
    {
        return response()->json([
            'count' => auth('tenant')->user()->unreadNotifications()->count()
        ]);
    }

    public function getLatestNotifications()
    {
        $notifications = auth('tenant')->user()->unreadNotifications()
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    public function markAsRead($id)
    {
        $notification = auth('tenant')->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        auth('tenant')->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    /**
     * Get unread notifications for dropdown display
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnread()
    {
        $user = auth('tenant')->user();
        $notifications = $user->unreadNotifications()
            ->latest()
            ->take(5)
            ->get();
            
        return response()->json([
            'count' => $user->unreadNotifications()->count(),
            'notifications' => $notifications
        ]);
    }
    
    /**
     * Create a new notification and broadcast it
     * 
     * @param mixed $notifiable The user to notify
     * @param \Illuminate\Notifications\Notification $notification The notification to send
     * @return void
     */
    public function createAndBroadcast($notifiable, $notification)
    {
        // Send the notification via database channel
        $notifiable->notify($notification);
        
        // Get the latest notification (the one we just created)
        $databaseNotification = $notifiable->notifications()
            ->latest()
            ->first();
            
        // Broadcast the notification to the user's private channel
        broadcast(new NotificationReceived($databaseNotification, $notifiable->id))->toOthers();
        
        return $databaseNotification;
    }
}
