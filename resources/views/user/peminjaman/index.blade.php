@extends('layouts.user')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-3">
        <h1 class="text-2xl sm:text-3xl font-semibold text-gray-800">
            📦 Daftar Peminjaman
        </h1>
        <a href="{{ route('user.peminjaman.dashboard') }}"
           class="bg-purple-600 hover:bg-purple-700 text-white text-sm sm:text-base px-4 sm:px-5 py-2.5 rounded-lg shadow transition-all duration-200">
            + Ajukan Peminjaman
        </a>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white p-4 rounded-xl shadow-sm mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form method="GET" action="{{ route('user.peminjaman.index') }}" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama barang..."
                   class="w-full sm:w-64 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-400 focus:border-purple-500 text-sm">

            <select name="status" class="w-full sm:w-40 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-400 focus:border-purple-500 text-sm">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>

            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-sm px-4 py-2 rounded-lg shadow">
                🔍 Cari
            </button>
        </form>

        @if(request('search') || request('status'))
            <a href="{{ route('user.peminjaman.index') }}"
               class="text-sm text-gray-500 hover:text-purple-600 mt-1 sm:mt-0">
               ❌ Reset Filter
            </a>
        @endif
    </div>

    <!-- Notifikasi -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-6 text-sm sm:text-base">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- Tabel Desktop -->
    <div class="hidden md:block bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full border-collapse">
            <thead class="bg-purple-600 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">#</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Barang</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Tanggal Pinjam</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Tanggal Kembali</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($peminjaman as $item)
                    <tr class="hover:bg-purple-50 transition-all duration-150">
                        <td class="px-6 py-3 text-gray-700">{{ $loop->iteration }}</td>
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $item->barang->nama_barang ?? '-' }}</td>
                        <td class="px-6 py-3 text-gray-700">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d M Y') }}</td>
                        <td class="px-6 py-3 text-gray-700">{{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->format('d M Y') : '-' }}</td>
                        <td class="px-6 py-3">
                            @if($item->status == 'pending')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-full">Menunggu</span>
                            @elseif($item->status == 'disetujui')
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">Disetujui</span>
                            @elseif($item->status == 'dipinjam')
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">Dipinjam</span>
                            @elseif($item->status == 'dikembalikan')
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-full">Dikembalikan</span>
                            @elseif($item->status == 'ditolak')
                                <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-medium rounded-full">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">
                            @if(in_array($item->status, ['disetujui', 'dipinjam']))
                                <a href="{{ route('user.peminjaman.kembalikan', $item->id) }}"
                                   class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg">
                                    🔁 Kembalikan
                                </a>
                            @elseif($item->status == 'dikembalikan')
                                <span class="text-gray-500 text-xs">Sudah dikembalikan</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-6">
                            Belum ada data peminjaman 😕
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-4">
        @forelse ($peminjaman as $item)
            <div class="bg-white rounded-xl shadow p-4 border border-gray-100">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="text-base font-semibold text-gray-800">{{ $item->barang->nama_barang ?? '-' }}</h3>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'disetujui' => 'bg-green-100 text-green-700',
                            'dipinjam' => 'bg-blue-100 text-blue-700',
                            'dikembalikan' => 'bg-gray-100 text-gray-700',
                            'ditolak' => 'bg-red-100 text-red-700'
                        ];
                    @endphp
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$item->status] ?? 'bg-gray-100 text-gray-600' }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </div>

                <p class="text-sm text-gray-600 mb-1">
                    <span class="font-medium text-gray-700">Tanggal Pinjam:</span>
                    {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d M Y') }}
                </p>
                <p class="text-sm text-gray-600 mb-2">
                    <span class="font-medium text-gray-700">Tanggal Kembali:</span>
                    {{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->format('d M Y') : '-' }}
                </p>

                <div class="flex justify-end">
                    @if(in_array($item->status, ['disetujui', 'dipinjam']))
                        <a href="{{ route('user.peminjaman.kembalikan', $item->id) }}"
                           class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg">
                           🔁 Kembalikan
                        </a>
                    @elseif($item->status == 'dikembalikan')
                        <span class="text-gray-500 text-xs">Sudah dikembalikan</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-gray-500 py-6">
                Belum ada data peminjaman 😕
            </div>
        @endforelse
    </div>

</div>
@endsection
