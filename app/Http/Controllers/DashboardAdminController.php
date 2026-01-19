<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class DashboardAdminController extends Controller
{
    public function index()
    {
        // Statistik umum
        $totalBarang = Barang::count();
        $tersedia = Barang::where('status', 'tersedia')->count();
        $dipinjam = \App\Models\Peminjaman::where('status', 'disetujui')->count();
        $diperbaiki = Barang::where('status', 'diperbaiki')->count();

        // Ambil 5 barang terbaru
        $latestBarang = Barang::latest()->take(5)->get();

        // Hitung stok tersedia real-time untuk setiap barang
        foreach ($latestBarang as $barang) {
            // Jumlah barang yang sedang dipinjam (status disetujui)
            $dipinjamCount = Peminjaman::where('barang_id', $barang->id)
                ->where('status', 'disetujui')
                ->sum('jumlah_pinjam');

            // Hitung stok tersedia real-time
            $barang->tersedia = max(
                $barang->jumlah - ($dipinjamCount + $barang->jumlah_rusak + $barang->jumlah_diperbaiki),
                0
            );
        }

        return view('admin.barang.dashboard', compact(
            'totalBarang', 'tersedia', 'dipinjam', 'diperbaiki', 'latestBarang'
        ));
    }
}
