<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Auth::user()
            ->notifications()
            ->when($request->unread, fn($q) => $q->where('is_read', false))
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function show(Notification $notification)
    {
        $user = Auth::user();
        
        if ($notification->user_id !== $user->id) {
            abort(403);
        }

        $notification->markAsRead();

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return back();
    }

    public function markAllRead()
    {
        Auth::user()
            ->notifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'Semua notifikasi telah dibaca');
    }

    public function destroy(Notification $notification)
    {
        $user = Auth::user();
        
        if ($notification->user_id !== $user->id) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', 'Notifikasi dihapus');
    }

    public function getUnreadCount()
    {
        return response()->json([
            'count' => Auth::user()->notifications()->where('is_read', false)->count()
        ]);
    }
}