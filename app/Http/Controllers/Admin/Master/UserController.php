<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user.
     */
    public function index()
    {
        $users = User::query()
            ->select([
                'id',
                'name',
                'email',
                'role',
                'status',
                'created_at',
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/MasterData/User/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Form tambah user.
     */
    public function create()
    {
        return Inertia::render('Admin/MasterData/User/Create', [
            'roles' => [
                [
                    'value' => 'admin',
                    'label' => 'Admin',
                ],
                [
                    'value' => 'klinik',
                    'label' => 'Petugas Klinik',
                ],
                [
                    'value' => 'tksi',
                    'label' => 'TKSI',
                ],
            ],
        ]);
    }

    /**
     * Simpan user baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'role' => [
                'required',
                'in:admin,klinik,tksi',
            ],

            'status' => [
                'required',
                'in:aktif,tidak_aktif',
            ],
        ]);

        $validated['password'] = bcrypt(
            $validated['password']
        );

        $user = User::create($validated);

        NotificationService::toUser(
            $user,
            'Selamat datang di SiKes-Boarding',
            'Akun Anda telah dibuat oleh Admin. Silakan gunakan akun ini untuk mengakses sistem.',
            'success',
            route('dashboard')
        );

        NotificationService::toRole(
            'admin',
            'User Baru Ditambahkan',
            "User {$user->name} dengan role {$user->role} telah ditambahkan.",
            'info',
            route('admin.master.user.index')
        );

        return redirect()
            ->route('admin.master.user.index')
            ->with(
                'success',
                'User berhasil ditambahkan.'
            );
    }

    /**
     * Form edit user.
     */
    public function edit(User $user)
    {
        return Inertia::render('Admin/MasterData/User/Edit', [
            'user' => $user,

            'roles' => [
                [
                    'value' => 'admin',
                    'label' => 'Admin',
                ],
                [
                    'value' => 'klinik',
                    'label' => 'Petugas Klinik',
                ],
                [
                    'value' => 'tksi',
                    'label' => 'TKSI',
                ],
            ],
        ]);
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],

            'role' => [
                'required',
                'in:admin,klinik,tksi',
            ],

            'status' => [
                'required',
                'in:aktif,tidak_aktif',
            ],
        ]);

        $user->update($validated);

        NotificationService::toUser(
            $user,
            'Data akun diperbarui',
            'Data akun Anda telah diperbarui oleh Admin.',
            'info',
            route('dashboard')
        );

        NotificationService::toRole(
            'admin',
            'User Diperbarui',
            "Data user {$user->name} telah diperbarui.",
            'info',
            route('admin.master.user.index')
        );

        return redirect()
            ->route('admin.master.user.index')
            ->with(
                'success',
                'Data user berhasil diperbarui.'
            );
    }

    /**
     * Hapus user.
     */
    public function destroy(User $user)
    {
        // Jangan izinkan user dengan ID 1 dihapus.
        if ($user->id === 1) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'User utama tidak dapat dihapus.'
                );
        }

        $deletedName = $user->name;
        $deletedRole = $user->role;
        $user->delete();

        NotificationService::toRole(
            'admin',
            'User Dihapus',
            "User {$deletedName} dengan role {$deletedRole} telah dihapus.",
            'warning',
            route('admin.master.user.index')
        );

        return redirect()
            ->route('admin.master.user.index')
            ->with(
                'success',
                'User berhasil dihapus.'
            );
    }
}