<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanAdminController extends Controller
{
    public function index()
    {
        // 🔹 Statistik utama
        $totalPeminjamanBulanIni = Peminjaman::whereMonth('created_at', now()->month)->count();
        $totalDikembalikan = Peminjaman::where('status', 'dikembalikan')->count();
        $barangRusak = Barang::where('status', 'rusak')->count();
        $userAktif = Peminjaman::select('user_id', DB::raw('COUNT(*) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->with('user')
            ->take(1)
            ->get()
            ->first();

        // 🔹 Grafik tren peminjaman (12 bulan terakhir)
        $peminjamanPerBulan = Peminjaman::select(
            DB::raw('MONTH(created_at) as bulan'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('created_at', now()->year)
        ->groupBy('bulan')
        ->orderBy('bulan')
        ->pluck('total', 'bulan')
        ->toArray();

        // 🔹 Barang paling sering dipinjam (Top 5)
        $topBarang = Peminjaman::select('barang_id', DB::raw('COUNT(*) as total'))
            ->groupBy('barang_id')
            ->orderByDesc('total')
            ->take(5)
            ->with('barang')
            ->get();

        // 🔹 User paling aktif (Top 5)
        $topUser = Peminjaman::select('user_id', DB::raw('COUNT(*) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->take(5)
            ->with('user')
            ->get();

        // 🔹 Insight otomatis
        $insight = [];
        if ($topBarang->isNotEmpty()) {
            $barangTerlaris = $topBarang->first()->barang->nama_barang ?? 'Barang A';
            $insight[] = "📈 Barang <b>{$barangTerlaris}</b> paling sering dipinjam bulan ini — mungkin stok perlu ditambah.";
        }

        if ($barangRusak > 0) {
            $insight[] = "⚠️ Ada <b>{$barangRusak}</b> barang rusak, cek kondisi di gudang segera.";
        }

        if ($userAktif && $userAktif->user) {
            $insight[] = "💪 User paling aktif: <b>{$userAktif->user->name}</b> dengan {$userAktif->total} kali peminjaman.";
        }

        if (empty($insight)) {
            $insight[] = "✨ Semua aman, aktivitas berjalan normal!";
        }

        return view('admin.barang.laporan', compact(
            'totalPeminjamanBulanIni',
            'totalDikembalikan',
            'barangRusak',
            'userAktif',
            'peminjamanPerBulan',
            'topBarang',
            'topUser',
            'insight'
        ));
    }
}
