<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    use HasFactory;

    protected $table = 'galeri';

    protected $fillable = [
        'type',
        'item_id',
        'foto',
        'urutan',
        'is_primary',
        'caption'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'urutan' => 'integer'
    ];

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
     * Helper untuk get full path foto
     */
    public function getFotoUrlAttribute()
    {
        return asset('uploads/galeri/' . $this->type . '/' . $this->foto);
    }

    /**
     * Scope untuk query galeri by type & item
     */
    public function scopeForItem($query, $type, $itemId)
    {
        return $query->where('type', $type)
                    ->where('item_id', $itemId)
                    ->orderBy('urutan');
    }

    /**
     * Scope untuk ambil foto primary
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}