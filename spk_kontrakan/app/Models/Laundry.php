<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laundry extends Model
{
    use HasFactory;

    protected $table = 'laundry';

    protected $fillable = [
        'nama',
        'alamat',
        'no_whatsapp',
        'latitude',
        'longitude',
        'jarak',
        'fasilitas',
        'jam_buka',
        'jam_tutup',
        'status',
        'foto',
    ];

    /**
     * Tambahkan foto_url ke semua JSON response secara otomatis
     */
    protected $appends = ['foto_url'];

    /**
     * Accessor: foto_url = full absolute URL foto utama laundry
     * Mengecek keberadaan file di disk agar URL selalu benar,
     * terlepas dari casing nama folder (Laundry vs laundry).
     */
    public function getFotoUrlAttribute(): ?string
    {
        if (empty($this->foto)) {
            return null;
        }

        $foto = $this->foto;

        if (str_starts_with($foto, 'http')) {
            return $foto;
        }

        // Cek keberadaan file di tiap kemungkinan folder
        $folders = ['uploads/Laundry', 'uploads/laundry'];
        foreach ($folders as $folder) {
            if (file_exists(public_path($folder . '/' . $foto))) {
                return url($folder . '/' . $foto);
            }
        }

        // Fallback: konstruksi URL dengan folder Laundry (standar saat ini)
        return url('uploads/Laundry/' . $foto);
    }

    /**
     * Relasi: 1 Laundry punya banyak Layanan
     */
    public function layanan()
    {
        return $this->hasMany(LayananLaundry::class);
    }

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
     * Relasi: 1 Laundry punya banyak Galeri Foto
     */
    public function galeri()
    {
        return $this->hasMany(Galeri::class, 'item_id')
                    ->where('type', 'laundry')
                    ->orderBy('urutan');
    }

    /**
     * Get foto primary/utama dari galeri
     */
    public function fotoPrimary()
    {
        return $this->hasOne(Galeri::class, 'item_id')
                    ->where('type', 'laundry')
                    ->where('is_primary', true);
    }

    /**
     * ========== RELASI REVIEWS ==========
     */
    
    /**
     * Relasi: 1 Laundry punya banyak Reviews
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'item_id')
                    ->where('type', 'laundry')
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
     * Relasi: 1 Laundry punya banyak Favorites
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'item_id')
                    ->where('type', 'laundry');
    }

    /**
     * Cek apakah laundry sudah difavoritkan oleh user tertentu
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
     * Relasi: 1 Laundry punya banyak Activity Logs
     */
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'model', 'model_type', 'model_id');
    }
}