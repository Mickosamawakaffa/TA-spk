<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontrakan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'alamat',
        'no_whatsapp',
        'latitude',
        'longitude',
        'harga',
        'jarak',
        'fasilitas',
        'jumlah_kamar',
        'bathroom_count',
        'foto',
        'status',
        'occupied_until',
    ];

    protected $casts = [
        'occupied_until' => 'date',
    ];

    /**
     * Generate Google Maps URL
     */
    public function getMapsUrlAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
        return null;
    }

    /**
     * ========== FITUR BARU: WHATSAPP ==========
     */

    /**
     * Generate WhatsApp URL untuk chat langsung
     */
    public function getWhatsappUrlAttribute()
    {
        if (!$this->no_whatsapp) {
            return null;
        }

        // Format nomor WhatsApp (hilangkan karakter selain angka)
        $cleanNumber = preg_replace('/[^0-9]/', '', $this->no_whatsapp);

        // Jika diawali 0, ganti dengan 62 (kode negara Indonesia)
        if (substr($cleanNumber, 0, 1) === '0') {
            $cleanNumber = '62' . substr($cleanNumber, 1);
        }

        // Jika belum ada kode negara, tambahkan 62
        if (substr($cleanNumber, 0, 2) !== '62') {
            $cleanNumber = '62' . $cleanNumber;
        }

        // Pesan default
        $message = urlencode("Halo, saya tertarik dengan kontrakan *{$this->nama}* di {$this->alamat}. Apakah masih tersedia?");

        return "https://wa.me/{$cleanNumber}?text={$message}";
    }

    /**
     * Format nomor WhatsApp untuk tampilan
     * Contoh: 081234567890 → 0812-3456-7890
     */
    public function getFormattedWhatsappAttribute()
    {
        if (!$this->no_whatsapp) {
            return null;
        }

        $number = $this->no_whatsapp;

        // Format: 08XX-XXXX-XXXX
        if (strlen($number) >= 10) {
            return substr($number, 0, 4) . '-' . substr($number, 4, 4) . '-' . substr($number, 8);
        }

        return $number;
    }

    /**
     * Cek apakah kontrakan punya nomor WhatsApp
     */
    public function hasWhatsapp()
    {
        return !empty($this->no_whatsapp);
    }

    /**
     * Hitung jarak dari koordinat user (dalam kilometer)
     * Menggunakan Haversine formula
     */
    public function calculateDistance($userLat, $userLng)
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        $earthRadius = 6371; // dalam kilometer

        $latFrom = deg2rad($userLat);
        $lonFrom = deg2rad($userLng);
        $latTo = deg2rad($this->latitude);
        $lonTo = deg2rad($this->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return round($angle * $earthRadius, 2); // Return dalam KM dengan 2 desimal
    }

    /**
     * ========== RELASI GALERI ==========
     */
    
    /**
     * Relasi: 1 Kontrakan punya banyak Galeri Foto
     */
    public function galeri()
    {
        return $this->hasMany(Galeri::class, 'item_id')
                    ->where('type', 'kontrakan')
                    ->orderBy('urutan');
    }

    /**
     * Get foto primary/utama dari galeri
     */
    public function fotoPrimary()
    {
        return $this->hasOne(Galeri::class, 'item_id')
                    ->where('type', 'kontrakan')
                    ->where('is_primary', true);
    }

    /**
     * ========== RELASI REVIEWS ==========
     */
    
    /**
     * Relasi: 1 Kontrakan punya banyak Reviews
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'item_id')
                    ->where('type', 'kontrakan')
                    ->with('user')
                    ->latest();
    }

    /**
     * Hitung rata-rata rating
     */
    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Hitung total reviews
     */
    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * ========== RELASI FAVORITES ==========
     */
    
    /**
     * Relasi: 1 Kontrakan punya banyak Favorites
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'item_id')
                    ->where('type', 'kontrakan');
    }

    /**
     * Cek apakah kontrakan sudah difavoritkan oleh user tertentu
     */
    public function isFavoritedBy($userId)
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }

    /**
     * Hitung total favorites
     */
    public function getTotalFavoritesAttribute()
    {
        return $this->favorites()->count();
    }

    /**
     * ========== RELASI ACTIVITY LOGS ==========
     */
    
    /**
     * Relasi: 1 Kontrakan punya banyak Activity Logs
     */
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'model', 'model_type', 'model_id');
    }

    /**
     * ========== RELASI BOOKINGS ==========
     */
    
    /**
     * Relasi: 1 Kontrakan punya banyak Bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get booking aktif saat ini (jika ada)
     */
    public function activeBooking()
    {
        return $this->hasOne(Booking::class)
                    ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_CHECKED_IN])
                    ->where('start_date', '<=', now()->toDateString())
                    ->where('end_date', '>=', now()->toDateString())
                    ->latest();
    }

    /**
     * ========== HELPER METHODS: STATUS & KETERSEDIAAN ==========
     */

    /**
     * Cek apakah kontrakan sedang terisi (berdasarkan booking aktif)
     */
    public function isCurrentlyOccupied(): bool
    {
        // Cek dari field status langsung
        if ($this->status === 'occupied') {
            // Double check occupied_until masih valid
            if ($this->occupied_until && $this->occupied_until->isPast()) {
                // Auto-reset jika sudah lewat
                $this->update(['status' => 'available', 'occupied_until' => null]);
                return false;
            }
            return true;
        }

        // Cek dari booking aktif
        return Booking::forKontrakan($this->id)
            ->currentlyActive()
            ->exists();
    }

    /**
     * Cek apakah kontrakan tersedia untuk periode tertentu
     */
    public function isAvailableFor($startDate, $endDate): bool
    {
        if ($this->status === 'maintenance') {
            return false;
        }

        return Booking::isAvailable($this->id, $startDate, $endDate);
    }

    /**
     * Get status label untuk UI
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'available' => 'Tersedia',
            'booked' => 'Dipesan',
            'occupied' => 'Terisi',
            'maintenance' => 'Pemeliharaan',
            default => ucfirst($this->status ?? 'available'),
        };
    }

    /**
     * Get status badge class untuk UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'available' => 'bg-success',
            'booked' => 'bg-warning text-dark',
            'occupied' => 'bg-danger',
            'maintenance' => 'bg-secondary',
            default => 'bg-success',
        };
    }

    /**
     * Sinkronkan status berdasarkan booking aktif
     */
    public function syncStatusFromBookings(): void
    {
        $activeBooking = Booking::forKontrakan($this->id)
            ->currentlyActive()
            ->first();

        if ($activeBooking) {
            if ($activeBooking->status === Booking::STATUS_CHECKED_IN) {
                $this->update([
                    'status' => 'occupied',
                    'occupied_until' => $activeBooking->end_date,
                ]);
            } else {
                $this->update([
                    'status' => 'booked',
                    'occupied_until' => $activeBooking->end_date,
                ]);
            }
        } else {
            // Tidak ada booking aktif
            if (in_array($this->status, ['booked', 'occupied'])) {
                $this->update([
                    'status' => 'available',
                    'occupied_until' => null,
                ]);
            }
        }
    }

    /**
     * Scope: Kontrakan yang tersedia
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope: Kontrakan yang terisi
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', 'occupied');
    }

    /**
     * ========== BATHROOM SCORING SYSTEM ==========
     */

    /**
     * Hitung skor kamar mandi berdasarkan jumlah dan rasio dengan kamar tidur
     * 
     * Formula:
     * - Base = 10 (jika bathroom_count >= 1)
     * - Extra = 5 × (bathroom_count - 1)
     * - Ratio bonus berdasarkan bathroom-to-bedroom ratio (BBR):
     *   * BBR >= 1.0 → +10 poin (sangat nyaman)
     *   * 0.5 <= BBR < 1.0 → +5 poin (standar)
     *   * BBR < 0.5 → +0 poin (terbatas)
     * 
     * @return int
     */
    public function getBathroomScoreAttribute()
    {
        $bathroomCount = $this->bathroom_count ?? 0;
        $bedroomCount = max(1, $this->jumlah_kamar ?? 1);

        // Jika tidak ada kamar mandi
        if ($bathroomCount == 0) {
            return 0;
        }

        // Base score
        $score = 10;

        // Extra points untuk setiap kamar mandi tambahan
        if ($bathroomCount > 1) {
            $score += 5 * ($bathroomCount - 1);
        }

        // Ratio bonus
        $bbr = $bathroomCount / $bedroomCount;
        
        if ($bbr >= 1.0) {
            $score += 10; // Sangat nyaman - setiap kamar punya akses kamar mandi
        } elseif ($bbr >= 0.5) {
            $score += 5; // Standar
        }
        // else: tidak ada bonus untuk BBR < 0.5

        return $score;
    }

    /**
     * Get label untuk skor kamar mandi
     * 
     * @return string
     */
    public function getBathroomLabelAttribute()
    {
        $score = $this->bathroom_score;

        return match(true) {
            $score == 0 => 'Tidak ada kamar mandi',
            $score <= 10 => 'Terbatas',
            $score <= 20 => 'Standar',
            $score <= 30 => 'Nyaman',
            default => 'Sangat Nyaman'
        };
    }

    /**
     * Get badge class untuk skor kamar mandi
     * 
     * @return string
     */
    public function getBathroomBadgeClassAttribute()
    {
        $score = $this->bathroom_score;

        return match(true) {
            $score == 0 => 'bg-danger',
            $score <= 10 => 'bg-warning',
            $score <= 20 => 'bg-info',
            $score <= 30 => 'bg-primary',
            default => 'bg-success'
        };
    }

    /**
     * Get icon untuk skor kamar mandi
     * 
     * @return string
     */
    public function getBathroomIconAttribute()
    {
        $score = $this->bathroom_score;

        return match(true) {
            $score == 0 => 'bi-x-circle',
            $score <= 10 => 'bi-droplet-half',
            $score <= 20 => 'bi-droplet',
            $score <= 30 => 'bi-stars',
            default => 'bi-gem'
        };
    }

    /**
     * Get deskripsi detail fasilitas kamar mandi
     * 
     * @return string
     */
    public function getBathroomDescriptionAttribute()
    {
        $bathroomCount = $this->bathroom_count ?? 0;
        $bedroomCount = $this->jumlah_kamar ?? 1;
        $bbr = $bathroomCount > 0 ? round($bathroomCount / $bedroomCount, 2) : 0;

        if ($bathroomCount == 0) {
            return 'Tidak ada kamar mandi';
        }

        if ($bathroomCount == 1) {
            return '1 kamar mandi';
        }

        $description = "{$bathroomCount} kamar mandi";

        // Tambahkan keterangan jika rasionya bagus
        if ($bbr >= 1.0) {
            $description .= ' — Setiap kamar punya akses kamar mandi';
        }

        return $description;
    }
}
