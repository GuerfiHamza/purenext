<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $notifications = Notification::where(function ($q) {
            $q->where('is_global', true)
              ->orWhere('user_id', auth()->id());
        })
        ->orderByDesc('created_at')
        ->take(20)
        ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $notifications->whereNull('read_at')->count(),
        ]);
    }

    public function markRead(Notification $notification): JsonResponse
    {
        $notification->update(['read_at' => now()]);
        return response()->json($notification);
    }

    public function markAllRead(): JsonResponse
    {
        Notification::where('is_global', true)
            ->orWhere('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'Toutes les notifications marquées comme lues.']);
    }

    public function destroy(Notification $notification): JsonResponse
    {
        $notification->delete();
        return response()->json(null, 204);
    }
}