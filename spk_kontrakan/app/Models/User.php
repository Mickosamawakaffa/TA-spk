<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
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
     * Append role_label dan user_type ke JSON response
     */
    protected $appends = ['role_label', 'user_type'];

    /**
     * Get role label accessor
     */
    public function getRoleLabelAttribute(): string
    {
        $role = $this->role ?? 'user';
        return match($role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'user' => 'Mahasiswa',
            default => $role,
        };
    }

    /**
     * Get user type accessor (untuk kompatibilitas API)
     */
    public function getUserTypeAttribute(): string
    {
        $role = $this->role ?? 'user';
        return $role === 'user' ? 'mahasiswa' : $role;
    }

    /**
     * Helper function untuk cek apakah user adalah Super Admin
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    /**
     * Helper function untuk cek apakah user adalah Admin biasa
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Helper function untuk cek apakah user adalah Mahasiswa (User biasa)
     *
     * @return bool
     */
    public function isUser()
    {
        return $this->role === 'user';
    }

    /**
     * Helper function untuk cek apakah user adalah Mahasiswa
     *
     * @return bool
     */
    public function isMahasiswa()
    {
        return $this->role === 'user';
    }

    /**
     * Get role label yang user-friendly
     *
     * @return string
     */
    public function getRoleLabel()
    {
        return match($this->role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'user' => 'Mahasiswa',
            default => $this->role,
        };
    }

    /**
     * Helper function untuk cek role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Relationship ke ActivityLog
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Relationship ke Booking
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Relationship ke Review
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relationship ke Favorite
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Device tokens untuk push notification admin.
     */
    public function adminDeviceTokens()
    {
        return $this->hasMany(AdminDeviceToken::class);
    }
}