@extends('layouts.user')

@section('content')
<div class="max-w-6xl p-6 mx-auto bg-white shadow-lg rounded-2xl md:p-8">
    <h1 class="flex items-center mb-6 text-2xl font-bold text-purple-800 md:text-3xl">
        🏠 <span class="ml-2">Dashboard Pengguna</span>
    </h1>

    <!-- Statistik -->
    <div class="grid grid-cols-1 gap-4 mb-8 md:grid-cols-3">
        <div class="p-4 bg-purple-100 border-l-4 border-purple-600 shadow rounded-xl">
            <p class="font-semibold text-gray-600">Total Peminjaman</p>
            <h2 id="user-total-peminjaman" class="text-3xl font-bold text-purple-700">{{ $totalPeminjaman }}</h2>
        </div>
        <div class="p-4 bg-yellow-100 border-l-4 border-yellow-500 shadow rounded-xl">
            <p class="font-semibold text-gray-600">Sedang Dipinjam</p>
            <h2 id="user-sedang-dipinjam" class="text-3xl font-bold text-yellow-600">{{ $sedangDipinjam }}</h2>
        </div>
        <div class="p-4 bg-green-100 border-l-4 border-green-600 shadow rounded-xl">
            <p class="font-semibold text-gray-600">Sudah Dikembalikan</p>
            <h2 id="user-sudah-dikembalikan" class="text-3xl font-bold text-green-700">{{ $sudahDikembalikan }}</h2>
        </div>
    </div>

    <!-- Tombol ke halaman peminjaman -->
    <div class="flex justify-end mb-6">
        <a href="{{ route('user.peminjaman.index') }}"
           class="px-5 py-2 font-semibold text-white transition bg-purple-600 rounded-lg shadow hover:bg-purple-700">
            📦 Lihat Peminjaman
        </a>
    </div>

    <!-- Daftar Barang -->
    <h2 class="flex items-center mb-4 text-xl font-semibold text-purple-800">
        💼 <span class="ml-2">Daftar Barang yang Tersedia</span>
    </h2>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
        @forelse ($barangTersedia as $item)
            @php
                $available = max(0, ($item->jumlah ?? 0) - ($item->jumlah_rusak ?? 0) - ($item->jumlah_diperbaiki ?? 0));
            @endphp
            <div class="p-5 transition transform shadow bg-gradient-to-br from-white to-purple-50 rounded-2xl hover:shadow-lg hover:-translate-y-1">

                <!-- Foto Barang -->
                <div class="flex items-center justify-center w-full h-40 mb-4 overflow-hidden border border-purple-100 rounded-xl bg-gray-50">
                    @if($item->foto_barang)
                        <img src="{{ asset('asset/' . basename($item->foto_barang)) }}"
                             alt="{{ $item->nama_barang }}"
                             class="object-cover w-full h-full">
                    @else
                        <span class="text-sm italic text-gray-400">Tidak ada foto</span>
                    @endif
                </div>

                <!-- Info Barang -->
                <h3 class="mb-2 text-lg font-bold text-center text-purple-700">{{ $item->nama_barang }}</h3>
                <p class="mb-1 text-sm text-gray-600">Kode: <span class="font-medium">{{ $item->kode_barang }}</span></p>
                <p class="mb-1 text-sm text-gray-600">Kategori: <span class="font-medium">{{ $item->kategori ?? '-' }}</span></p>
                <p class="mb-1 text-sm text-gray-600">Jumlah Total: <span class="font-medium">{{ $item->jumlah ?? 0 }}</span></p>
                <p class="mb-3 text-sm text-gray-600">Ready: <span class="font-medium text-green-700" data-barang-ready="{{ $item->id }}">{{ $available }}</span> unit</p>

                <div class="flex items-center justify-between">
                    <span class="inline-block px-3 py-1 text-sm font-semibold text-green-700 bg-green-100 rounded-full">
                        {{ ucfirst($item->status) }}
                    </span>
                    <a href="{{ route('user.peminjaman.create', ['barang_id' => $item->id]) }}"
                       class="px-4 py-2 font-semibold text-white transition bg-purple-600 rounded-lg hover:bg-purple-700">
                        📌 Pinjam
                    </a>
                </div>
            </div>
        @empty
            <p class="italic text-gray-500">Tidak ada barang yang tersedia saat ini.</p>
        @endforelse
    </div>
</div>

{{-- small script to allow incoming admin approve responses to update user dashboard if the same page is open --}}
<script>
/* If server returns counts + barang info, earlier functions applyCounts/applyBarangUpdate can be reused.
   But since this file may be loaded separately, we provide small helpers similar to those in admin peminjaman view.
*/
function updateUserStatsFromAdmin(counts) {
    if (!counts) return;
    // if these counts correspond to peminjaman totals you'd want to map them appropriately
    // for safety, we only update user counters for 'sedang dipinjam' if present (depends on your counting logic)
    // Here we don't have direct mapping, so skip unless backend sends explicit fields for user stats.
}
</script>
@endsection
