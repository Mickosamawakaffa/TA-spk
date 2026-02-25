<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
     * Helper function untuk cek apakah user adalah User biasa
     *
     * @return bool
     */
    public function isUser()
    {
        return $this->role === 'user';
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
}