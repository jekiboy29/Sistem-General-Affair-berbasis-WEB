<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barangs';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'jumlah',
        'jumlah_rusak',
        'jumlah_diperbaiki',
        'kondisi',
        'lokasi',
        'status',
        'foto_barang',
    ];

    // Relasi ke peminjaman
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'barang_id');
    }

    public function getTersediaAttribute()
    {
        $dipinjam = Peminjaman::where('barang_id', $this->id)
            ->where('status', 'disetujui')
            ->sum('jumlah_pinjam');

        return $this->jumlah - $dipinjam;
    }

}
