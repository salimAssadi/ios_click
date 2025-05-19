<?php

namespace Modules\Document\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Modules\Document\Events\NotificationReceived;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth('tenant')->user();
        $notifications = DatabaseNotification::on('tenant')
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('document::notifications.index', compact('notifications'));
    }

    public function getUnreadCount()
    {
        $user = auth('tenant')->user();
        $count = DatabaseNotification::on('tenant')
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->count();
        return response()->json([
            'count' => $count
        ]);
    }

    public function getLatestNotifications()
    {
        $user = auth('tenant')->user();
        $notifications = DatabaseNotification::on('tenant')
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->latest()
            ->take(5)
            ->get();
        return response()->json([
            'notifications' => $notifications
        ]);
    }

    public function markAsRead($id)
    {
        $notification = DatabaseNotification::on('tenant')
            ->where('notifiable_type', get_class(auth('tenant')->user()))
            ->where('notifiable_id', auth('tenant')->user()->id)
            ->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = auth('tenant')->user();
        DatabaseNotification::on('tenant')
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    /**
     * Get unread notifications for dropdown display
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    // public function getUnread()
    // {
    //     $user = auth('tenant')->user();
        
    //     $notifications = $user->unreadNotifications()
    //         ->latest()
    //         ->take(5)
    //         ->get();
            
    //     return response()->json([
    //         'count' => $user->unreadNotifications()->count(),
    //         'notifications' => $notifications
    //     ]);
    // }
    public function getUnread()
{
    $user = auth('tenant')->user();

    // Explicitly use the current connection
    $notifications = DatabaseNotification::on('tenant')
        ->where('notifiable_type', get_class($user))
        ->where('notifiable_id', $user->id)
        ->whereNull('read_at')
        ->latest()
        ->take(5)
        ->get();

    $count = DatabaseNotification::on('tenant')
        ->where('notifiable_type', get_class($user))
        ->where('notifiable_id', $user->id)
        ->whereNull('read_at')
        ->count();

    return response()->json([
        'count' => $count,
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
