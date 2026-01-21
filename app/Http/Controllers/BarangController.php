<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BarangController extends Controller
{
    // ========================
    // 📊 DASHBOARD BARANG (DENGAN FILTER & STOK REAL-TIME)
    // ========================
    public function dashboard(Request $request)
    {
        $query = Barang::query();

        // 🔍 Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🔎 Filter berdasarkan pencarian (nama atau kode)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        $latestBarang = $query->latest()->get();

        // ✅ Hitung total statistik
        $totalBarang = Barang::count();
        $tersedia = Barang::where('status', 'tersedia')->count();
        $dipinjam = Barang::where('status', 'dipinjam')->count();
        $diperbaiki = Barang::where('status', 'diperbaiki')->count();

        // ✅ Hitung stok tersedia secara real-time untuk setiap barang
        foreach ($latestBarang as $barang) {
            // Total dipinjam dengan status disetujui
            $dipinjamCount = Peminjaman::where('barang_id', $barang->id)
                ->where('status', 'disetujui')
                ->sum('jumlah_pinjam');

            // Hitung stok tersedia (real-time)
            $stokTersedia = $barang->jumlah
                - $dipinjamCount
                - ($barang->jumlah_rusak ?? 0)
                - ($barang->jumlah_diperbaiki ?? 0);

            // Pastikan tidak negatif
            $barang->stok_tersedia = max($stokTersedia, 0);

            // Tambahkan agar mudah dipanggil di blade
            $barang->stok_display = "{$barang->stok_tersedia} / {$barang->jumlah}";
        }

        return view('admin.barang.dashboard', compact(
            'latestBarang',
            'totalBarang',
            'tersedia',
            'dipinjam',
            'diperbaiki'
        ));
    }

    // ========================
    // 📦 INDEX (List Barang)
    // ========================
    public function index()
    {
        $barangs = Barang::latest()->get();
        return view('admin.barang.index', compact('barangs'));
    }

    // ========================
    // ➕ FORM TAMBAH BARANG
    // ========================
    public function create()
    {
        return view('admin.barang.create');
    }

    // ========================
    // 💾 SIMPAN BARANG BARU
    // ========================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|unique:barangs,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'required|in:tersedia,dipinjam,diperbaiki',
            'cropped_image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('cropped_image')) {
            $file = $request->file('cropped_image');

            $filename = 'barang_' . Str::random(12) . '.' . $file->getClientOriginalExtension();

            $destination = public_path('asset');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $path = 'asset/' . $filename;
        }

        Barang::create([
            'kode_barang' => $validated['kode_barang'],
            'nama_barang' => $validated['nama_barang'],
            'kategori' => $validated['kategori'] ?? null,
            'jumlah' => $validated['jumlah'],
            'jumlah_rusak' => 0,
            'jumlah_diperbaiki' => 0,
            'kondisi' => $validated['kondisi'],
            'lokasi' => $validated['lokasi'] ?? null,
            'status' => $validated['status'],
            'foto_barang' => $path,
        ]);

        return redirect()->route('admin.barang.dashboard')->with('success', 'Barang berhasil ditambahkan!');
    }

    // ========================
    // ✏️ FORM EDIT BARANG
    // ========================
    public function edit(Barang $barang)
    {
        return view('admin.barang.edit', compact('barang'));
    }

    // ========================
    // 🔁 UPDATE DATA BARANG
    // ========================
    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'status' => 'required|in:tersedia,dipinjam,diperbaiki',
            'cropped_image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

       if ($request->hasFile('cropped_image')) {

        if ($barang->foto_barang) {
            $oldPath = public_path($barang->foto_barang);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $file = $request->file('cropped_image');
        $filename = 'barang_' . Str::random(12) . '.' . $file->getClientOriginalExtension();

        $destination = public_path('asset');
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $file->move($destination, $filename);

        $validated['foto_barang'] = 'asset/' . $filename;
    }

        $barang->update($validated);

        return redirect()->route('admin.barang.dashboard')->with('success', 'Data barang berhasil diperbarui!');
    }

    // ========================
    // 🗑️ HAPUS BARANG
    // ========================
    public function destroy(Barang $barang)
    {
        if ($barang->foto_barang && Storage::exists('public/' . $barang->foto_barang)) {
            Storage::delete('public/' . $barang->foto_barang);
        }

        $barang->delete();

        return redirect()->route('admin.barang.dashboard')->with('success', 'Barang berhasil dihapus!');
    }
}
