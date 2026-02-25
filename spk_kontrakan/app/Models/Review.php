<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'item_id',
        'user_id',
        'rating',
        'review'
    ];

    protected $casts = [
        'rating' => 'integer'
    ];

    /**
     * Relationship ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship ke Kontrakan
     */
    public function kontrakan()
    {
        return $this->belongsTo(Kontrakan::class, 'item_id')->where('type', 'kontrakan');
    }

    /**
     * Relationship ke Laundry
     */
    public function laundry()
    {
        return $this->belongsTo(Laundry::class, 'item_id')->where('type', 'laundry');
    }

    /**
     * Scope untuk query review by type & item
     */
    public function scopeForItem($query, $type, $itemId)
    {
        return $query->where('type', $type)
                    ->where('item_id', $itemId)
                    ->with('user')
                    ->latest();
    }
}