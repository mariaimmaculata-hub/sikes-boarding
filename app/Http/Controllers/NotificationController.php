<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $notifications = Notification::query()
            ->where(function ($query) use ($user) {
                $query->where('role', $user->role)
                    ->orWhereNull('role');
            })
            ->latest()
            ->get();

        $unreadCount = Notification::query()
            ->where(function ($query) use ($user) {
                $query->where('role', $user->role)
                    ->orWhereNull('role');
            })
            ->whereNull('read_at')
            ->count();

        return Inertia::render('Notifikasi/Index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'role' => $user->role,
        ]);
    }
}