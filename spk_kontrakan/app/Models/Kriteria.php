<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;

    protected $table = 'kriteria';

    protected $fillable = [
        'tipe_bisnis',      // TAMBAHKAN INI
        'nama_kriteria',
        'bobot',
        'tipe',
        'keterangan',
    ];
}