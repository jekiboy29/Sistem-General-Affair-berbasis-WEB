<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanAdminController extends Controller
{
    // 🔹 Menampilkan semua peminjaman
    public function index()
    {
        $peminjaman = Peminjaman::with(['user', 'barang'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistik — hanya hitung peminjaman yang belum dikembalikan
        $total = Peminjaman::whereNotIn('status', ['dikembalikan'])->count();
        $pending = Peminjaman::where('status', 'pending')->count();
        $disetujui = Peminjaman::where('status', 'disetujui')->count();
        $ditolak = Peminjaman::where('status', 'ditolak')->count();
        $dikembalikan = Peminjaman::where('status', 'dikembalikan')->count();

        return view('admin.barang.peminjaman', compact(
            'peminjaman', 'total', 'pending', 'disetujui', 'ditolak', 'dikembalikan'
        ));
    }

    // 🔹 Setujui peminjaman (update stok otomatis)
    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('barang')->findOrFail($id);
            $barang = $peminjaman->barang;

            if ($barang) {
                $barang->jumlah = max(0, $barang->jumlah - $peminjaman->jumlah_pinjam);
                $barang->save();
            }

            $peminjaman->status = 'disetujui';
            $peminjaman->save();

            DB::commit();

            // Update statistik (hanya yang belum dikembalikan)
            $counts = [
                'total' => Peminjaman::whereNotIn('status', ['dikembalikan'])->count(),
                'pending' => Peminjaman::where('status', 'pending')->count(),
                'disetujui' => Peminjaman::where('status', 'disetujui')->count(),
                'ditolak' => Peminjaman::where('status', 'ditolak')->count(),
            ];

            $available = $barang ? max(
                0,
                $barang->jumlah - ($barang->jumlah_rusak ?? 0) - ($barang->jumlah_diperbaiki ?? 0)
            ) : null;

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman disetujui dan stok barang diperbarui.',
                'new_status' => 'disetujui',
                'counts' => $counts,
                'barang' => [
                    'id' => $barang ? $barang->id : null,
                    'jumlah' => $barang ? $barang->jumlah : null,
                    'available' => $available,
                ],
                'peminjaman_id' => $peminjaman->id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses permintaan.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // 🔹 Tolak peminjaman
    public function reject($id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            $peminjaman->status = 'ditolak';
            $peminjaman->save();

            $counts = [
                'total' => Peminjaman::whereNotIn('status', ['dikembalikan'])->count(),
                'pending' => Peminjaman::where('status', 'pending')->count(),
                'disetujui' => Peminjaman::where('status', 'disetujui')->count(),
                'ditolak' => Peminjaman::where('status', 'ditolak')->count(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Peminjaman ditolak.',
                'new_status' => 'ditolak',
                'counts' => $counts,
                'peminjaman_id' => $peminjaman->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses permintaan.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // 🔹 Update status manual (opsional)
    public function updateStatus(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status = $request->status;
        $peminjaman->save();

        return response()->json(['success' => true]);
    }

    // 🔹 Hapus data peminjaman
    public function destroy($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->delete();

        return response()->json(['message' => 'Data peminjaman dihapus.']);
    }
}
