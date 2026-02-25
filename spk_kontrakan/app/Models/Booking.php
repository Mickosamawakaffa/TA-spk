<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Booking extends Model
{
    use HasFactory;

    /**
     * Boot method - Auto sync kontrakan status saat booking di-update atau dihapus
     */
    protected static function boot()
    {
        parent::boot();

        // Saat booking dihapus, otomatis update status kontrakan
        static::deleted(function ($booking) {
            static::syncKontrakanStatus($booking->kontrakan_id);
        });
    }

    /**
     * Sinkronisasi status kontrakan berdasarkan booking aktif
     * Dipanggil otomatis setelah booking dihapus/cancel/checkout
     */
    public static function syncKontrakanStatus($kontrakanId)
    {
        // Cek apakah ada booking checked_in
        $hasCheckedIn = static::where('kontrakan_id', $kontrakanId)
            ->where('status', self::STATUS_CHECKED_IN)
            ->exists();

        if ($hasCheckedIn) {
            Kontrakan::where('id', $kontrakanId)->update(['status' => 'occupied']);
            return;
        }

        // Cek apakah ada booking confirmed atau pending
        $hasActiveOrPending = static::where('kontrakan_id', $kontrakanId)
            ->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_PENDING])
            ->exists();

        if ($hasActiveOrPending) {
            Kontrakan::where('id', $kontrakanId)->update(['status' => 'booked']);
            return;
        }

        // Tidak ada booking aktif → kontrakan tersedia
        Kontrakan::where('id', $kontrakanId)->update([
            'status' => 'available',
            'occupied_until' => null,
        ]);
    }

    protected $fillable = [
        'kontrakan_id',
        'user_id',
        'start_date',
        'end_date',
        'status',
        'amount',
        'payment_status',
        'payment_method',
        'payment_proof',
        'paid_at',
        'notes',
        'booking_source',
        'tenant_name',
        'tenant_phone',
        'confirmed_at',
        'checked_in_at',
        'checked_out_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // ========== STATUS CONSTANTS ==========
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CHECKED_IN = 'checked_in';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_UNPAID = 'unpaid';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_REFUNDED = 'refunded';

    // ========== RELASI ==========

    /**
     * Booking dimiliki oleh satu Kontrakan
     */
    public function kontrakan()
    {
        return $this->belongsTo(Kontrakan::class);
    }

    /**
     * Booking dimiliki oleh satu User (penyewa)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========== SCOPES ==========

    /**
     * Scope: Booking yang aktif (confirmed atau checked_in)
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_CONFIRMED, self::STATUS_CHECKED_IN]);
    }

    /**
     * Scope: Booking yang pending
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: Booking yang sudah dibatalkan
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope: Booking yang overlap dengan periode tertentu
     * Digunakan untuk cek konflik
     */
    public function scopeOverlapping(Builder $query, $startDate, $endDate, $excludeId = null): Builder
    {
        $query->where(function ($q) use ($startDate, $endDate) {
            // Cek overlap: start1 <= end2 AND end1 >= start2
            $q->where('start_date', '<=', $endDate)
              ->where('end_date', '>=', $startDate);
        });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query;
    }

    /**
     * Scope: Booking untuk kontrakan tertentu
     */
    public function scopeForKontrakan(Builder $query, $kontrakanId): Builder
    {
        return $query->where('kontrakan_id', $kontrakanId);
    }

    /**
     * Scope: Booking yang mencakup hari ini
     */
    public function scopeCurrentlyActive(Builder $query): Builder
    {
        $today = now()->toDateString();
        return $query->active()
                     ->where('start_date', '<=', $today)
                     ->where('end_date', '>=', $today);
    }

    // ========== HELPERS ==========

    /**
     * Cek apakah booking ini aktif (confirmed atau checked_in)
     */
    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_CONFIRMED, self::STATUS_CHECKED_IN]);
    }

    /**
     * Cek apakah booking mencakup hari ini
     */
    public function isCurrentlyOccupying(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $today = now()->toDateString();
        return $this->start_date->lte($today) && $this->end_date->gte($today);
    }

    /**
     * Cek apakah booking bisa dibatalkan
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    /**
     * Hitung durasi sewa dalam hari
     */
    public function getDurationDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    /**
     * Hitung durasi sewa dalam bulan (pembulatan)
     */
    public function getDurationMonthsAttribute(): float
    {
        return round($this->start_date->diffInDays($this->end_date) / 30, 1);
    }

    /**
     * Get status label untuk UI
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Konfirmasi',
            self::STATUS_CONFIRMED => 'Dikonfirmasi',
            self::STATUS_CHECKED_IN => 'Sedang Ditempati',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status badge class untuk UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-warning text-dark',
            self::STATUS_CONFIRMED => 'bg-info',
            self::STATUS_CHECKED_IN => 'bg-success',
            self::STATUS_COMPLETED => 'bg-secondary',
            self::STATUS_CANCELLED => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_UNPAID => 'Belum Bayar',
            self::PAYMENT_PAID => 'Lunas',
            self::PAYMENT_REFUNDED => 'Dikembalikan',
            default => ucfirst($this->payment_status),
        };
    }

    // ========== ACTIONS ==========

    /**
     * Konfirmasi booking
     */
    public function confirm(): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->status = self::STATUS_CONFIRMED;
        $this->confirmed_at = now();
        $this->save();

        // Sync status kontrakan otomatis
        static::syncKontrakanStatus($this->kontrakan_id);

        return true;
    }

    /**
     * Check-in (penyewa masuk)
     */
    public function checkIn(): bool
    {
        if ($this->status !== self::STATUS_CONFIRMED) {
            return false;
        }

        $this->status = self::STATUS_CHECKED_IN;
        $this->checked_in_at = now();
        $this->save();

        // Sync status kontrakan otomatis + set occupied_until
        $this->kontrakan->update(['occupied_until' => $this->end_date]);
        static::syncKontrakanStatus($this->kontrakan_id);

        return true;
    }

    /**
     * Check-out (penyewa keluar)
     */
    public function checkOut(): bool
    {
        if ($this->status !== self::STATUS_CHECKED_IN) {
            return false;
        }

        $this->status = self::STATUS_COMPLETED;
        $this->checked_out_at = now();
        $this->save();

        // Sync status kontrakan otomatis
        static::syncKontrakanStatus($this->kontrakan_id);

        return true;
    }

    /**
     * Batalkan booking
     */
    public function cancel(?string $reason = null): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->status = self::STATUS_CANCELLED;
        $this->cancelled_at = now();
        $this->cancellation_reason = $reason;
        $this->save();

        // Sync status kontrakan otomatis
        static::syncKontrakanStatus($this->kontrakan_id);

        return true;
    }

    /**
     * Tandai sebagai lunas
     */
    public function markAsPaid(?string $method = null): bool
    {
        $this->payment_status = self::PAYMENT_PAID;
        $this->paid_at = now();
        $this->payment_method = $method;
        return $this->save();
    }

    // ========== STATIC HELPERS ==========

    /**
     * Cek apakah ada konflik booking untuk kontrakan & periode tertentu
     */
    public static function hasConflict($kontrakanId, $startDate, $endDate, $excludeId = null): bool
    {
        return static::forKontrakan($kontrakanId)
            ->active()
            ->overlapping($startDate, $endDate, $excludeId)
            ->exists();
    }

    /**
     * Cek ketersediaan kontrakan untuk periode tertentu
     */
    public static function isAvailable($kontrakanId, $startDate, $endDate, $excludeId = null): bool
    {
        return !static::hasConflict($kontrakanId, $startDate, $endDate, $excludeId);
    }
}
