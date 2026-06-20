<?php

namespace App\Http\Controllers;

use App\Models\PushNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = PushNotification::whereNull('customer_id')
            ->latest()
            ->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    public function unread()
    {
        $notifications = PushNotification::whereNull('customer_id')
            ->where('is_read', false)
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'count' => PushNotification::whereNull('customer_id')->where('is_read', false)->count(),
            'notifications' => $notifications
        ]);
    }

    public function markAsRead($id)
    {
        $notification = PushNotification::findOrFail($id);
        $notification->update([
            'is_read' => true,
            'read_at' => now()
        ]);
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        PushNotification::whereNull('customer_id')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $notification = PushNotification::findOrFail($id);
        $notification->delete();
        return response()->json(['success' => true]);
    }

    public function deleteAll()
    {
        PushNotification::whereNull('customer_id')->delete();
        return response()->json(['success' => true]);
    }
}
