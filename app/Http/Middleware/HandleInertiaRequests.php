<?php

namespace App\Http\Middleware;

use App\Models\Notification;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),

            /*
            |--------------------------------------------------------------------------
            | AUTH
            |--------------------------------------------------------------------------
            */

            'auth' => [
                'user' => $request->user(),
            ],

            /*
            |--------------------------------------------------------------------------
            | JUMLAH NOTIFIKASI BELUM DIBACA
            |--------------------------------------------------------------------------
            */

            'notificationCount' => fn () => $request->user()
                ? Notification::query()
                    ->where('user_id', $request->user()->id)
                    ->whereNull('read_at')
                    ->count()
                : 0,

            /*
            |--------------------------------------------------------------------------
            | 3 NOTIFIKASI TERBARU
            |--------------------------------------------------------------------------
            */

            'recentNotifications' => fn () => $request->user()
                ? Notification::query()
                    ->where('user_id', $request->user()->id)
                    ->latest()
                    ->limit(2)
                    ->get()
                    ->map(fn (Notification $notification) => [
                        'id' => $notification->id,

                        'title' => $notification->title,

                        'message' => $notification->message,

                        'type' => $notification->type,

                        'role' => $notification->role,

                        'url' => $notification->url,

                        'read_at' => $notification->read_at?->toIso8601String(),

                        'read' => !is_null(
                            $notification->read_at
                        ),

                        'created_at' => $notification->created_at
                            ? $notification->created_at->format(
                                'd/m/Y H:i'
                            )
                            : null,
                    ])
                    ->values()
                : [],
        ];
    }
}