<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananLaundry extends Model
{
    use HasFactory;

    protected $table = 'layanan_laundry';

    protected $fillable = [
        'laundry_id',
        'jenis_layanan',
        'nama_paket',
        'harga',
        'estimasi_selesai',
        'deskripsi',
        'status',
        'rating',
        'waktu_proses',
    ];

    // Relasi: Layanan ini milik 1 Laundry
    public function laundry()
    {
        return $this->belongsTo(Laundry::class);
    }
}