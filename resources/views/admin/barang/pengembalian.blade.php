@extends('layouts.admin')
@section('title', 'Manajemen Pengembalian Barang')

@section('content')
<div class="p-4 sm:p-6 bg-white rounded-2xl shadow">
    <h1 class="text-2xl font-bold text-purple-800 mb-4 flex items-center gap-2">🔁 Manajemen Pengembalian Barang</h1>

    {{-- Statistik --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="p-3 sm:p-4 bg-purple-100 rounded-xl text-center shadow">
            <h2 class="text-xs sm:text-sm text-purple-800">Total</h2>
            <p class="text-lg sm:text-2xl font-bold text-purple-700">{{ $stats['total'] }}</p>
        </div>
        <div class="p-3 sm:p-4 bg-yellow-100 rounded-xl text-center shadow">
            <h2 class="text-xs sm:text-sm text-yellow-800">Pending</h2>
            <p id="pending-count" class="text-lg sm:text-2xl font-bold text-yellow-700">{{ $stats['pending'] }}</p>
        </div>
        <div class="p-3 sm:p-4 bg-blue-100 rounded-xl text-center shadow">
            <h2 class="text-xs sm:text-sm text-blue-800">Sesuai</h2>
            <p id="disetujui-count" class="text-lg sm:text-2xl font-bold text-blue-700">{{ $stats['verified'] }}</p>
        </div>
        <div class="p-3 sm:p-4 bg-red-100 rounded-xl text-center shadow">
            <h2 class="text-xs sm:text-sm text-red-800">Tidak Sesuai</h2>
            <p id="ditolak-count" class="text-lg sm:text-2xl font-bold text-red-700">{{ $stats['not_sesuai'] }}</p>
        </div>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg shadow-sm">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg shadow-sm">{{ session('error') }}</div>
    @endif

    {{-- Tabel --}}
    <div class="overflow-x-auto hidden sm:block">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">#</th>
                    <th class="p-3 text-left">Nama Barang</th>
                    <th class="p-3 text-left">Peminjam</th>
                    <th class="p-3 text-left">Tanggal</th>
                    <th class="p-3 text-left">Kondisi</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengembalian as $item)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3">{{ $loop->iteration }}</td>
                        <td class="p-3">{{ $item->peminjaman->barang->nama_barang ?? '-' }}</td>
                        <td class="p-3">{{ $item->peminjaman->user->name ?? '-' }}</td>
                        <td class="p-3">{{ $item->created_at->format('d M Y') }}</td>
                        <td class="p-3">{{ Str::limit($item->kondisi ?? '-', 30) }}</td>
                        <td class="p-3">
                            @if ($item->status_verifikasi === 'verified')
                                <span class="text-green-600 font-semibold">✅ Sesuai</span>
                            @elseif ($item->status_verifikasi === 'not_sesuai')
                                <span class="text-red-600 font-semibold">❌ Tidak Sesuai</span>
                            @else
                                <span class="text-yellow-600 font-semibold">⏳ Pending</span>
                            @endif
                        </td>
                        <td class="p-3 text-center">
                            <button onclick="showDetail({{ $item->id }})"
                                class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded-lg shadow">
                                👁️ Lihat Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-4 text-center text-gray-500">Belum ada pengembalian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal --}}
<div id="detailModal" class="fixed inset-0 hidden bg-black/50 z-50 items-center justify-center">
    <div class="bg-white rounded-xl p-6 w-11/12 sm:w-1/2 shadow-lg relative">
        <button onclick="closeModal()" class="absolute top-2 right-3 text-gray-400 hover:text-gray-600">✖</button>
        <h2 class="text-lg font-bold text-purple-800 mb-3">Detail Pengembalian</h2>
        <div id="detailContent" class="text-sm space-y-2 text-gray-700">
            <p>Memuat data...</p>
        </div>
        <div class="mt-4 flex justify-end gap-2">
            <button id="btnSesuai" onclick="verifikasi('sesuai')" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">✅ Sesuai</button>
            <button id="btnTidakSesuai" onclick="verifikasi('tidak_sesuai')" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">❌ Tidak Sesuai</button>
        </div>
    </div>
</div>

<script>
let currentId = null;

function showDetail(id) {
    currentId = id;
    const modal = document.getElementById('detailModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('detailContent').innerHTML = "<p>🔄 Memuat data...</p>";

    fetch(`/admin/barang/pengembalian/${id}`)
        .then(res => res.json())
        .then(data => {
            const fotoHTML = data.foto_barang
                ? `<img src="/storage/${data.foto_barang}" class="rounded-lg mt-2 w-full sm:w-2/3">`
                : `<p class="italic text-gray-500 mt-1">Tidak ada foto yang diunggah.</p>`;

            document.getElementById('detailContent').innerHTML = `
                <p><strong>Barang:</strong> ${data.peminjaman.barang.nama_barang}</p>
                <p><strong>Peminjam:</strong> ${data.peminjaman.user.name}</p>
                <p><strong>Kondisi:</strong> ${data.kondisi}</p>
                ${fotoHTML}
            `;
        })
        .catch(() => {
            document.getElementById('detailContent').innerHTML = "<p class='text-red-600'>Gagal memuat detail pengembalian.</p>";
        });
}

function closeModal() {
    const modal = document.getElementById('detailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function verifikasi(status) {
    if (!confirm(`Yakin ingin menandai barang ini sebagai "${status.replace('_', ' ')}"?`)) return;

    fetch(`/admin/barang/pengembalian/${currentId}/verify`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status })
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            closeModal();
            location.reload();
        } else {
            alert(result.message || 'Gagal memproses verifikasi.');
        }
    })
    .catch(() => alert('Terjadi kesalahan jaringan.'));
}
</script>
@endsection
