<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'item_id',
        'user_id'
    ];

    /**
     * Relationship ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship ke Kontrakan (tanpa where constraint)
     */
    public function kontrakan()
    {
        return $this->belongsTo(Kontrakan::class, 'item_id');
    }

    /**
     * Relationship ke Laundry (tanpa where constraint)
     */
    public function laundry()
    {
        return $this->belongsTo(Laundry::class, 'item_id');
    }
    
    /**
     * Get the related item (kontrakan atau laundry)
     */
    public function getItemAttribute()
    {
        if ($this->type === 'kontrakan') {
            return $this->kontrakan;
        } elseif ($this->type === 'laundry') {
            return $this->laundry;
        }
        return null;
    }

    /**
     * Scope untuk query favorite by user
     */
    public function scopeForUser($query, $userId, $type = null)
    {
        $query = $query->where('user_id', $userId);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query;
    }

    /**
     * Check apakah item sudah difavoritkan oleh user
     */
    public static function isFavorited($type, $itemId, $userId)
    {
        return self::where('type', $type)
                  ->where('item_id', $itemId)
                  ->where('user_id', $userId)
                  ->exists();
    }
}