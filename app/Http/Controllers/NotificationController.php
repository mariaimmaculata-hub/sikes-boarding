<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    /**
     * ============================================================
     * DAFTAR NOTIFIKASI
     * ============================================================
     */
    public function index(): Response
    {
        $user = auth()->user();

        $notifications = Notification::query()
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(fn (Notification $notification) => [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'role' => $notification->role,
                'url' => $notification->url,
                'read_at' => $notification->read_at?->toIso8601String(),
                'read' => !is_null($notification->read_at),
                'created_at' => $notification->created_at?->format('d/m/Y H:i'),
            ]);

        return Inertia::render('Notifikasi/Index', [
            'notifications' => $notifications,

            'unreadCount' => Notification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count(),

            'role' => $user->role,
        ]);
    }


    /**
     * ============================================================
     * DETAIL NOTIFIKASI
     * ============================================================
     */
    public function show(string $id)
    {
        $user = auth()->user();

        $notification = Notification::query()
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Otomatis tandai sudah dibaca
        if (!$notification->read_at) {
            $notification->update([
                'read_at' => now(),
            ]);
        }

        return Inertia::render('Notifikasi/Show', [
            'notification' => [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'role' => $notification->role,
                'url' => $notification->url,
                'read_at' => $notification->read_at?->toIso8601String(),
                'created_at' => $notification->created_at?->format('d/m/Y H:i'),
            ],
        ]);
    }


    /**
     * ============================================================
     * TANDAI SATU NOTIFIKASI SUDAH DIBACA
     * ============================================================
     */
    public function markRead(Notification $notification): RedirectResponse
    {
        abort_unless(
            $notification->user_id === auth()->id(),
            403
        );

        if (!$notification->read_at) {
            $notification->update([
                'read_at' => now(),
            ]);
        }

        return back();
    }


    /**
     * ============================================================
     * TANDAI SEMUA SUDAH DIBACA
     * ============================================================
     */
    public function markAllRead(): RedirectResponse
    {
        Notification::query()
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return back();
    }


    /**
     * ============================================================
     * HAPUS NOTIFIKASI TERPILIH
     * ============================================================
     *
     * Frontend mengirim:
     *
     * {
     *     "ids": [
     *         "id-notifikasi-1",
     *         "id-notifikasi-2"
     *     ]
     * }
     *
     * Hanya notifikasi milik user yang sedang login yang boleh
     * dihapus.
     */
    public function destroyMultiple(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
                'min:1',
            ],

            'ids.*' => [
                'required',
            ],
        ]);

        Notification::query()
            ->where('user_id', $user->id)
            ->whereIn('id', $validated['ids'])
            ->delete();

        return back();
    }
}