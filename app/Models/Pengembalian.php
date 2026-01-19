<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalian';

    protected $fillable = [
        'peminjaman_id',
        'kondisi',
        'foto_barang',
        'status_verifikasi',
    ];

    // Relasi ke tabel peminjaman
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    // Relasi ke tabel barang (lewat peminjaman)
    public function barang()
    {
        return $this->hasOneThrough(
            Barang::class,
            Peminjaman::class,
            'id',           // Foreign key di tabel peminjaman
            'id',           // Foreign key di tabel barang
            'peminjaman_id',// Local key di tabel pengembalian
            'barang_id'     // Local key di tabel peminjaman
        );
    }

    // Relasi ke tabel user (lewat peminjaman)
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Peminjaman::class,
            'id',           // Foreign key di tabel peminjaman
            'id',           // Foreign key di tabel user
            'peminjaman_id',// Local key di tabel pengembalian
            'user_id'       // Local key di tabel peminjaman
        );
    }
}
