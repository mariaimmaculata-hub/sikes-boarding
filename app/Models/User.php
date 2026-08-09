<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'status',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Periode yang dibuat oleh user.
     */
    public function periodes(): HasMany
    {
        return $this->hasMany(Periode::class, 'created_by');
    }

    /**
     * Pemeriksaan berkala yang dilakukan oleh user.
     */
    public function pemeriksaanBerkala(): HasMany
    {
        return $this->hasMany(
            PemeriksaanBerkala::class,
            'pemeriksa_id'
        );
    }

    /**
     * Kunjungan klinik yang diperiksa oleh user.
     */
    public function kunjunganKlinik(): HasMany
    {
        return $this->hasMany(
            KunjunganKlinik::class,
            'pemeriksa_id'
        );
    }
}