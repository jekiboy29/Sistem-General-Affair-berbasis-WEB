@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6 animate-fadeIn">

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="p-5 bg-gradient-to-br from-purple-500 to-purple-700 text-white rounded-2xl shadow-lg">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-sm font-medium opacity-80">Total Barang</h2>
                <span class="bg-white bg-opacity-20 px-2 py-1 text-xs rounded">📦</span>
            </div>
            <!-- added id -->
            <p id="totalBarang" class="text-4xl font-bold">{{ $totalBarang }}</p>
        </div>

        <div class="p-5 bg-gradient-to-br from-green-500 to-green-700 text-white rounded-2xl shadow-lg">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-sm font-medium opacity-80">Tersedia</h2>
                <span class="bg-white bg-opacity-20 px-2 py-1 text-xs rounded">✅</span>
            </div>
            <!-- added id -->
            <p id="tersediaCount" class="text-4xl font-bold">{{ $tersedia }}</p>
        </div>

        <div class="p-5 bg-gradient-to-br from-yellow-400 to-yellow-600 text-white rounded-2xl shadow-lg">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-sm font-medium opacity-80">Dipinjam</h2>
                <span class="bg-white bg-opacity-20 px-2 py-1 text-xs rounded">📋</span>
            </div>
            <!-- added id -->
            <p id="dipinjamCount" class="text-4xl font-bold">{{ $dipinjam }}</p>
        </div>

        <div class="p-5 bg-gradient-to-br from-red-500 to-red-700 text-white rounded-2xl shadow-lg">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-sm font-medium opacity-80">Diperbaiki</h2>
                <span class="bg-white bg-opacity-20 px-2 py-1 text-xs rounded">🛠️</span>
            </div>
            <p id="diperbaikiCount" class="text-4xl font-bold">{{ $diperbaiki }}</p>
        </div>
    </div>

    <!-- Ringkasan Data Barang -->
    <div class="bg-white rounded-2xl shadow-md p-6 relative">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-3">
            <h2 class="text-xl font-bold text-purple-700">Daftar Barang</h2>

            <!-- Filter & Search -->
            <form method="GET" action="{{ route('admin.barang.dashboard') }}" class="flex flex-wrap items-center gap-3">
                <select name="status" onchange="this.form.submit()"
                    class="border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="diperbaiki" {{ request('status') == 'diperbaiki' ? 'selected' : '' }}>Diperbaiki</option>
                </select>

                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama/kode..."
                           class="px-3 py-2 text-sm focus:outline-none w-40 sm:w-48">
                    <button type="submit" class="px-3 text-purple-600 hover:text-purple-800">🔍</button>
                </div>

                <a href="{{ route('admin.barang.create') }}"
                   class="hidden sm:inline-block bg-purple-600 text-white px-4 py-2 rounded-lg shadow hover:bg-purple-700 transition text-sm">
                    + Tambah Barang
                </a>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-purple-50 text-purple-700">
                        <th class="p-3 font-semibold">No</th>
                        <th class="p-3 font-semibold">Foto</th>
                        <th class="p-3 font-semibold">Kode</th>
                        <th class="p-3 font-semibold">Nama</th>
                        <th class="p-3 font-semibold">Stok</th>
                        <th class="p-3 font-semibold">Status</th>
                        <th class="p-3 font-semibold"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestBarang as $index => $barang)
                        @php
                            // compute available for display (jumlah - rusak - diperbaiki)
                            $available = max(0, ($barang->jumlah ?? 0) - ($barang->jumlah_rusak ?? 0) - ($barang->jumlah_diperbaiki ?? 0));
                        @endphp
                        <tr class="border-b hover:bg-purple-50 transition" data-barang-id="{{ $barang->id }}">
                            <td class="p-3">{{ $index + 1 }}</td>
                            <td class="p-3">
                                @if($barang->foto_barang)
                                    <img src="{{ asset('storage/' . $barang->foto_barang) }}"
                                         alt="Foto {{ $barang->nama_barang }}"
                                         class="w-12 h-12 rounded-lg object-cover border border-purple-200">
                                @else
                                    <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-gray-100 text-gray-400 text-xs">
                                        No Foto
                                    </div>
                                @endif
                            </td>
                            <td class="p-3">{{ $barang->kode_barang }}</td>
                            <td class="p-3 font-semibold">{{ $barang->nama_barang }}</td>
                            <td class="p-3 text-sm text-gray-700">
                                {{-- added id and data-total so JS can update --}}
                                <span id="barang-{{ $barang->id }}-stok" data-total="{{ $barang->jumlah }}">{{ $available }} / {{ $barang->jumlah }}</span>
                            </td>
                            <td class="p-3">
                                @if($barang->status == 'tersedia')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Tersedia</span>
                                @elseif($barang->status == 'dipinjam')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs">Dipinjam</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs">Diperbaiki</span>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                <a href="{{ route('admin.barang.edit', $barang->id) }}"
                                   class="text-purple-600 hover:underline text-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-4 text-gray-500">Belum ada data barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Floating button -->
    <a href="{{ route('admin.barang.create') }}"
       class="sm:hidden fixed bottom-6 right-6 bg-purple-600 hover:bg-purple-700 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-xl transition-all duration-300 animate-bounce-smooth">
        <span class="text-4xl font-bold leading-none">+</span>
    </a>
</div>

<style>
@keyframes bounce-smooth {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}
.animate-bounce-smooth {
    animation: bounce-smooth 1.5s infinite;
}
</style>
@endsection
