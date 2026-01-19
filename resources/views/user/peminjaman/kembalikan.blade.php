@extends('layouts.user')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-6 mt-8">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">🔁 Form Pengembalian Barang</h2>

    <div class="mb-4">
        <label class="block text-gray-600 text-sm font-medium mb-1">Nama Barang</label>
        <input type="text" value="{{ $peminjaman->barang->nama_barang }}" readonly 
               class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-gray-700">
    </div>

    <div class="mb-4">
        <label class="block text-gray-600 text-sm font-medium mb-1">Kode Barang</label>
        <input type="text" value="{{ $peminjaman->barang->kode_barang }}" readonly 
               class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-gray-700">
    </div>

    {{-- Form pengembalian --}}
    <form action="{{ route('user.peminjaman.kembalikan', $peminjaman->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-600 text-sm font-medium mb-1">Kondisi Barang Saat Dikembalikan</label>
            <textarea name="kondisi" rows="4" 
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:outline-none"
                      placeholder="Contoh: Barang dalam kondisi baik, tidak ada kerusakan" required></textarea>
        </div>

        <div class="mb-6">
            <label class="block text-gray-600 text-sm font-medium mb-1">Upload Foto Barang (opsional)</label>
            <input type="file" name="foto_barang" accept="image/*"
                   class="w-full text-gray-700 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:outline-none">
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('user.peminjaman.index') }}" 
               class="text-gray-600 hover:text-gray-800 text-sm">⬅ Kembali</a>
            <button type="submit" 
                    class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-lg shadow transition-all duration-200">
                Kirim Pengembalian
            </button>
        </div>
    </form>
</div>
@endsection
