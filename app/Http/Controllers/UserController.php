<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;

class UserController extends Controller
{
    /**
     * Dashboard pengguna (user).
     */
    public function dashboard()
    {
        $userId = auth()->id();

        // Ambil data statistik sederhana
        $totalPeminjaman = Peminjaman::where('user_id', $userId)->count();
        $dipinjam = Peminjaman::where('user_id', $userId)
                              ->whereIn('status', ['pending', 'disetujui'])
                              ->count();
        $dikembalikan = Peminjaman::where('user_id', $userId)
                                  ->where('status', 'dikembalikan')
                                  ->count();

        // Ambil 5 data terbaru
        $recent = Peminjaman::where('user_id', $userId)
                            ->latest()
                            ->take(5)
                            ->get();

        return view('user.dashboard', compact(
            'totalPeminjaman',
            'dipinjam',
            'dikembalikan',
            'recent'
        ));
    }
}
