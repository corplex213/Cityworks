<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        $type = $request->query('type', 'all');
        $query = Notification::where('user_id', Auth::id());

        if ($type !== 'all') {
            if ($type === 'mentions') {
                $query->whereIn('type', ['comment_added', 'comment_reply']);
            } elseif ($type === 'tasks') {
                $query->whereIn('type', ['task_assigned', 'task_completed']);
            } else {
                $query->where('type', $type);
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->count();

        return response()->json([
            'count' => $count
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->update(['read' => true]);

        return response()->json([
            'success' => true
        ]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json([
            'success' => true
        ]);
    }

    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true
        ]);
    }

    public function deleteAll()
    {
        Notification::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true
        ]);
    }
} 