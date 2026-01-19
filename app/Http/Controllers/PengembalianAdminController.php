<?php

namespace App\Http\Controllers;

use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\TelegramHelper;

class PengembalianAdminController extends Controller
{
    /**
     * 🔹 Tampilkan daftar semua pengembalian (termasuk yang sudah diverifikasi)
     */
    public function index()
    {
        // Ambil semua pengembalian, bukan cuma pending
        $pengembalian = Pengembalian::with(['barang', 'user'])
            ->latest()
            ->get();

        // Statistik pengembalian
        $stats = [
            'total' => Pengembalian::count(),
            'pending' => Pengembalian::where('status_verifikasi', 'pending')->count(),
            'verified' => Pengembalian::where('status_verifikasi', 'verified')->count(),
            'not_sesuai' => Pengembalian::where('status_verifikasi', 'not_sesuai')->count(),
        ];

        return view('admin.barang.pengembalian', compact('pengembalian', 'stats'));
    }

    /**
     * 🔹 Detail pengembalian (untuk modal)
     */
    public function show($id)
    {
        $data = Pengembalian::with(['peminjaman.user', 'peminjaman.barang'])->findOrFail($id);
        return response()->json($data);
    }

    /**
     * 🔹 Verifikasi pengembalian (AJAX)
     */
    public function verify(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:sesuai,tidak_sesuai',
        ]);

        $pengembalian = Pengembalian::with(['peminjaman.user', 'peminjaman.barang'])->findOrFail($id);
        $peminjaman = $pengembalian->peminjaman;
        $barang = $peminjaman->barang;
        $user = $peminjaman->user;

        try {
            if ($request->status === 'sesuai') {
                $pengembalian->status_verifikasi = 'verified';
                $barang->jumlah += $peminjaman->jumlah_pinjam;
                $barang->save();

                // Update status peminjaman jadi dikembalikan
                $peminjaman->status = 'dikembalikan';
                $peminjaman->save();
            } else {
                $pengembalian->status_verifikasi = 'not_sesuai';

                try {
                    if (class_exists(TelegramHelper::class)) {
                        TelegramHelper::sendPengembalianNotification($user->name, $barang->nama_barang);
                    }
                } catch (\Exception $te) {
                    Log::warning('Gagal kirim notifikasi Telegram: ' . $te->getMessage());
                }
            }

            $pengembalian->save();

            return response()->json([
                'success' => true,
                'message' => 'Status verifikasi diperbarui.',
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal verifikasi pengembalian: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses verifikasi.',
            ], 500);
        }
    }
}
