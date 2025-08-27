<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Events\NotificationCreated;

class NotificationService
{
    /**
     * Create a notification for a specific user
     *
     * @param int $userId
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @return Notification
     */
    public function createForUser($userId, $type, $title, $message, $link = null)
    {
        \Log::info("Creating notification: Type=$type, Title=$title for user $userId");
        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'read' => false,
        ]);
        event(new NotificationCreated($notification));
        return $notification;
    }

    /**
     * Create a notification for all users
     *
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @return void
     */
    public function createForAllUsers($type, $title, $message, $link = null)
    {
        $users = User::all();
        
        foreach ($users as $user) {
            $this->createForUser($user->id, $type, $title, $message, $link);
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
        $notification = Notification::find($notificationId);
        
        if (!$notification) {
            return false;
        }
        
        $notification->update(['read' => true]);
        return true;
    }

    /**
     * Mark all notifications as read for a user
     *
     * @param int $userId
     * @return int
     */
    public function markAllAsRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->update(['read' => true]);
    }

    /**
     * Get unread notifications count for a user
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadCount($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->count();
    }
} 