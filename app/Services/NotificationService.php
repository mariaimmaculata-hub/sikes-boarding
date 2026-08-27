<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Str;

class NotificationService
{
    public static function toRole(
        string $role,
        string $title,
        string $message,
        string $type = 'info',
        ?string $url = null
    ): void {
        User::query()
            ->where('role', $role)
            ->where('status', 'aktif')
            ->each(function (User $user) use ($role, $title, $message, $type, $url) {
                self::toUser($user, $title, $message, $type, $url);
            });
    }

    public static function toRoles(
        array $roles,
        string $title,
        string $message,
        string $type = 'info',
        ?string $url = null
    ): void {
        foreach (array_unique($roles) as $role) {
            self::toRole($role, $title, $message, $type, $url);
        }
    }

    public static function toUser(
        User $user,
        string $title,
        string $message,
        string $type = 'info',
        ?string $url = null
    ): void {
        Notification::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'role' => $user->role,
            'url' => $url,
        ]);
    }

    public static function toUserId(
        int $userId,
        string $title,
        string $message,
        string $type = 'info',
        ?string $url = null
    ): void {
        $user = User::find($userId);
        if ($user) {
            self::toUser($user, $title, $message, $type, $url);
        }
    }
}
