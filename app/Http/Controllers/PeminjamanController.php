<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Barang;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\PengembalianCreated;

class PeminjamanController extends Controller
{
    // 🔹 Dashboard user
    public function dashboard()
    {
        $userId = auth()->id();

        // Hanya hitung peminjaman aktif (belum dikembalikan)
        $totalPeminjaman = Peminjaman::where('user_id', $userId)
            ->whereIn('status', ['pending', 'disetujui', 'dipinjam'])
            ->count();

        $sedangDipinjam = Peminjaman::where('user_id', $userId)
            ->where('status', 'dipinjam')
            ->count();

        $sudahDikembalikan = Peminjaman::where('user_id', $userId)
            ->where('status', 'dikembalikan')
            ->count();

        // Hitung stok barang tersedia secara dinamis
        $barangTersedia = Barang::all()->filter(function ($barang) {
            $totalDipinjam = Peminjaman::where('barang_id', $barang->id)
                ->whereIn('status', ['dipinjam', 'disetujui'])
                ->sum('jumlah_pinjam');

            $barang->tersedia = max(0, $barang->jumlah - $barang->jumlah_rusak - $totalDipinjam);

            return $barang->tersedia > 0 && !in_array($barang->status, ['rusak', 'tidak bisa dipinjam']);
        });

        return view('user.peminjaman.dashboard', compact(
            'totalPeminjaman',
            'sedangDipinjam',
            'sudahDikembalikan',
            'barangTersedia'
        ));
    }

        public function getStats()
    {
        $userId = auth()->id();

        $totalPeminjaman = Peminjaman::where('user_id', $userId)->count();
        $sedangDipinjam = Peminjaman::where('user_id', $userId)->where('status', 'dipinjam')->count();
        $sudahDikembalikan = Peminjaman::where('user_id', $userId)->where('status', 'dikembalikan')->count();

        return response()->json([
            'totalPeminjaman' => $totalPeminjaman,
            'sedangDipinjam' => $sedangDipinjam,
            'sudahDikembalikan' => $sudahDikembalikan,
        ]);
    }

    // 🔹 Form pengajuan
    public function create(Request $request)
    {
        $barangs = Barang::where('status', 'tersedia')->get();
        $selectedBarangId = $request->barang_id;
        return view('user.peminjaman.create', compact('barangs', 'selectedBarangId'));
    }

    // 🔹 Simpan pengajuan
    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'jumlah_pinjam' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'nullable|date|after_or_equal:tanggal_pinjam',
            'tujuan' => 'nullable|string|max:255',
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        // Hitung stok ready sebelum menyimpan
        $totalDipinjam = Peminjaman::where('barang_id', $barang->id)
            ->whereIn('status', ['dipinjam', 'disetujui'])
            ->sum('jumlah_pinjam');

        $tersedia = $barang->jumlah - $barang->jumlah_rusak - $totalDipinjam;

        if ($validated['jumlah_pinjam'] > $tersedia) {
            return back()->with('error', 'Jumlah pinjaman melebihi stok tersedia (' . $tersedia . ' unit).')->withInput();
        }

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        Peminjaman::create($validated);

        return redirect()->route('user.peminjaman.index')->with('success', 'Pengajuan peminjaman berhasil dikirim!');
    }

    // 🔹 Daftar peminjaman
    public function index(Request $request)
    {
        $query = Peminjaman::with('barang')
            ->where('user_id', auth()->id());

        // Filter berdasarkan pencarian barang
        if ($request->filled('search')) {
            $query->whereHas('barang', function ($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $peminjaman = $query->latest()->get();

        return view('user.peminjaman.index', compact('peminjaman'));
    }

    // 🔹 Form pengembalian
    public function showFormKembalikan($id)
    {
        $peminjaman = Peminjaman::with('barang')->findOrFail($id);

        if ($peminjaman->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak');
        }

        return view('user.peminjaman.kembalikan', compact('peminjaman'));
    }

    // 🔹 Simpan data pengembalian (BARU)
    public function storeKembalikan(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $request->validate([
            'kondisi' => 'required|string|max:500',
            'foto_barang' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload foto jika ada
        $path = null;
        if ($request->hasFile('foto_barang')) {
            $path = $request->file('foto_barang')->store('pengembalian', 'public');
        }

        // Simpan ke tabel pengembalian
        \App\Models\Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'kondisi' => $request->kondisi,
            'foto_barang' => $path,
            'status_verifikasi' => 'pending',
        ]);

        // Update status peminjaman juga
        $peminjaman->update([
            'status' => 'dikembalikan',
            'tanggal_kembali' => now(),
        ]);

        return redirect()->route('user.peminjaman.index')
            ->with('success', 'Barang berhasil dikembalikan dan menunggu verifikasi admin!');
    }




}
