<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataBarangSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('barangs')->insert([
            [
                'kode_barang' => 'BRG001',
                'nama_barang' => 'Mouse',
                'jumlah' => 5,
                'status' => 'tersedia',
                'jumlah_rusak' => 0,
                'jumlah_diperbaiki' => 0,
                'jumlah_dipinjam' => 0,
                'jumlah_tersedia' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_barang' => 'BRG002',
                'nama_barang' => 'keyboard',
                'jumlah' => 7,
                 'status' => 'diperbaiki',
                'jumlah_rusak' => 7,
                'jumlah_diperbaiki' => 7,
                'jumlah_dipinjam' => 0,
                'jumlah_tersedia' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_barang' => 'BRG003',
                'nama_barang' => 'buku',
                'jumlah' => 10,
                 'status' => 'dipinjam',
                'jumlah_rusak' => 0,
                'jumlah_diperbaiki' => 0,
                'jumlah_dipinjam' => 10,
                'jumlah_tersedia' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_barang' => 'BRG004',
                'nama_barang' => 'kursi lipat',
                'jumlah' => 30,
                 'status' => 'rusak',
                'jumlah_rusak' => 5,
                'jumlah_diperbaiki' => 0,
                'jumlah_dipinjam' => 0,
                'jumlah_tersedia' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
