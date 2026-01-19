@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto bg-white shadow-lg rounded-2xl p-6 md:p-8">
    <h1 class="text-3xl font-bold text-purple-800 mb-6 flex items-center">
        📊 <span class="ml-2">Laporan Aktivitas Peminjaman & Pengembalian</span>
    </h1>

    {{-- Statistik Utama --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <x-stat-card title="Peminjaman Bulan Ini" value="{{ $totalPeminjamanBulanIni }}" color="purple" icon="📦" />
        <x-stat-card title="Total Dikembalikan" value="{{ $totalDikembalikan }}" color="green" icon="🔁" />
        <x-stat-card title="Barang Rusak" value="{{ $barangRusak }}" color="red" icon="⚙️" />
        <x-stat-card title="User Paling Aktif" value="{{ $userAktif->user->name ?? '-' }}" color="yellow" icon="👑" />
    </div>

    {{-- Grafik Tren Peminjaman --}}
    <div class="bg-purple-50 rounded-xl p-6 shadow mb-8">
        <h2 class="text-xl font-semibold text-purple-800 mb-4">📈 Tren Peminjaman per Bulan</h2>
        <canvas id="chartPeminjaman" height="100"></canvas>
    </div>

    {{-- Barang & User Teraktif --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white border rounded-xl p-6 shadow">
            <h2 class="text-lg font-semibold text-purple-800 mb-3">🏆 Top 5 Barang Terbanyak Dipinjam</h2>
            @foreach ($topBarang as $item)
                <p class="mb-2">{{ $loop->iteration }}. {{ $item->barang->nama_barang ?? '-' }}
                    <span class="text-gray-500">({{ $item->total }}x)</span>
                </p>
            @endforeach
        </div>
        <div class="bg-white border rounded-xl p-6 shadow">
            <h2 class="text-lg font-semibold text-purple-800 mb-3">👥 Top 5 User Teraktif</h2>
            @foreach ($topUser as $user)
                <p class="mb-2">{{ $loop->iteration }}. {{ $user->user->name ?? '-' }}
                    <span class="text-gray-500">({{ $user->total }}x)</span>
                </p>
            @endforeach
        </div>
    </div>

    {{-- Insight Otomatis --}}
    <div class="bg-gradient-to-r from-purple-50 to-purple-100 border-l-4 border-purple-500 rounded-xl p-5 shadow">
        <h2 class="text-lg font-semibold text-purple-800 mb-3">💡 Insight Sistem</h2>
        <ul class="list-disc list-inside text-gray-700 leading-relaxed">
            @foreach ($insight as $note)
                <li>{!! $note !!}</li>
            @endforeach
        </ul>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartPeminjaman').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($peminjamanPerBulan)) !!},
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: {!! json_encode(array_values($peminjamanPerBulan)) !!},
                backgroundColor: '#7e22ce',
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection

{{-- Komponen Statistik Card --}}
@once
    @push('components')
        @component('components.stat-card')
        @endcomponent
    @endpush
@endonce
