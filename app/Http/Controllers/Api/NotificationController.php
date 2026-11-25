<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShipmentNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * List authenticated user's notifications.
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = ShipmentNotification::with('shipment:id,shipment_number')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->take(30)
            ->get();

        $unreadCount = ShipmentNotification::where('user_id', $request->user()->id)
            ->unread()
            ->count();

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $unreadCount,
        ], 200);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, ShipmentNotification $notification): JsonResponse
    {
        if ((int) $notification->user_id !== (int) $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak.',
            ], 403);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sudah dibaca.',
        ], 200);
    }

    /**
     * Mark all notifications as read for current user.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        ShipmentNotification::where('user_id', $request->user()->id)
            ->unread()
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi ditandai sudah dibaca.',
        ], 200);
    }
}
