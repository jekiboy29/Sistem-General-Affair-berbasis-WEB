@extends('layouts.admin')
@section('title', 'Manajemen Peminjaman')

@section('content')
<div class="p-4 sm:p-6 bg-white rounded-2xl shadow">
    <h1 class="text-2xl font-bold text-purple-800 mb-4 flex items-center gap-2">
        📦 Manajemen Peminjaman
    </h1>

    {{-- Statistik --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="p-3 sm:p-4 bg-purple-100 rounded-xl text-center shadow">
            <h2 class="text-xs sm:text-sm text-purple-800">Total</h2>
            <p id="stat-total" class="text-lg sm:text-2xl font-bold text-purple-700">{{ $total }}</p>
        </div>
        <div class="p-3 sm:p-4 bg-yellow-100 rounded-xl text-center shadow">
            <h2 class="text-xs sm:text-sm text-yellow-800">Pending</h2>
            <p id="pending-count" class="text-lg sm:text-2xl font-bold text-yellow-700">{{ $pending }}</p>
        </div>
        <div class="p-3 sm:p-4 bg-blue-100 rounded-xl text-center shadow">
            <h2 class="text-xs sm:text-sm text-blue-800">Disetujui</h2>
            <p id="disetujui-count" class="text-lg sm:text-2xl font-bold text-blue-700">{{ $disetujui }}</p>
        </div>
        <div class="p-3 sm:p-4 bg-red-100 rounded-xl text-center shadow">
            <h2 class="text-xs sm:text-sm text-red-800">Ditolak</h2>
            <p id="ditolak-count" class="text-lg sm:text-2xl font-bold text-red-700">{{ $ditolak }}</p>
        </div>
    </div>

    {{-- === Tabel desktop === --}}
    <div class="hidden sm:block overflow-x-auto rounded-xl border border-gray-200">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">#</th>
                    <th class="p-3 text-left">Nama Barang</th>
                    <th class="p-3 text-left">Peminjam</th>
                    <th class="p-3 text-left">Jumlah</th>
                    <th class="p-3 text-left">Tanggal Pinjam</th>
                    <th class="p-3 text-left">Tanggal Kembali</th>
                    <th class="p-3 text-left">Tujuan</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($peminjaman as $item)
                    <tr id="row-{{ $item->id }}" class="border-t hover:bg-gray-50 transition">
                        <td class="p-3">{{ $loop->iteration }}</td>
                        <td class="p-3">{{ $item->barang->nama_barang ?? '-' }}</td>
                        <td class="p-3">{{ $item->user->name ?? '-' }}</td>
                        <td class="p-3">{{ $item->jumlah_pinjam }}</td>
                        <td class="p-3">{{ $item->tanggal_pinjam }}</td>
                        <td class="p-3">{{ $item->tanggal_kembali ?? '-' }}</td>
                        <td class="p-3">{{ $item->tujuan ?? '-' }}</td>

                        {{-- status cell (dynamic) --}}
                        <td id="status-{{ $item->id }}" class="p-3 capitalize font-semibold
                            @if($item->status == 'pending') text-yellow-600
                            @elseif($item->status == 'disetujui') text-blue-600
                            @elseif($item->status == 'dipinjam') text-green-600
                            @elseif($item->status == 'dikembalikan') text-gray-600
                            @elseif($item->status == 'ditolak') text-red-600
                            @endif">
                            {{ $item->status }}
                        </td>

                        {{-- actions cell (dynamic) --}}
                        <td id="actions-{{ $item->id }}" class="p-3 flex justify-center gap-2">
                            {{-- If pending => show Setujui + Tolak
                                 Otherwise => show Edit button --}}
                            @if($item->status === 'pending')
                                <button onclick="setujuiPeminjaman({{ $item->id }})"
                                    class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded-lg shadow transition">
                                    ✅ Setujui
                                </button>
                                <button onclick="tolakPeminjaman({{ $item->id }})"
                                    class="bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1 rounded-lg shadow transition">
                                    ❌ Tolak
                                </button>
                            @else
                                <button onclick="openEditModal({{ $item->id }})"
                                    class="bg-gray-500 hover:bg-gray-600 text-white text-sm px-3 py-1 rounded-lg shadow transition">
                                    ✏️ Edit
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="p-4 text-center text-gray-500">Belum ada data peminjaman.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- === Modal Edit Status (hidden by default) === --}}
<div id="editModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-md p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Ubah Status Peminjaman</h3>
        <p id="modal-info" class="text-sm text-gray-600 mb-4">Pilih aksi untuk peminjaman ini.</p>

        <div class="flex gap-3">
            <button id="modal-approve-btn" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">✅ Setujui</button>
            <button id="modal-reject-btn" class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">❌ Tolak</button>
        </div>

        <div class="mt-4 text-right">
            <button onclick="closeEditModal()" class="px-4 py-1 rounded bg-gray-200 hover:bg-gray-300">Batal</button>
        </div>
    </div>
</div>

{{-- === SCRIPT === --}}
<script>
    // current editing id for modal
    let currentEditId = null;

    function updateStatsUI(counts) {
        if (!counts) return;
        document.getElementById('stat-total').innerText = counts.total ?? document.getElementById('stat-total').innerText;
        document.getElementById('pending-count').innerText = counts.pending ?? document.getElementById('pending-count').innerText;
        document.getElementById('disetujui-count').innerText = counts.disetujui ?? document.getElementById('disetujui-count').innerText;
        document.getElementById('ditolak-count').innerText = counts.ditolak ?? document.getElementById('ditolak-count').innerText;
    }

    function applyStatusToRow(id, newStatus) {
        const statusCell = document.getElementById(`status-${id}`);
        if (!statusCell) return;

        // reset classes (simple way: set className)
        let base = 'p-3 capitalize font-semibold';
        let colorClass = '';
        if (newStatus === 'pending') colorClass = ' text-yellow-600';
        else if (newStatus === 'disetujui') colorClass = ' text-blue-600';
        else if (newStatus === 'dipinjam') colorClass = ' text-green-600';
        else if (newStatus === 'dikembalikan') colorClass = ' text-gray-600';
        else if (newStatus === 'ditolak') colorClass = ' text-red-600';

        statusCell.className = base + colorClass;
        statusCell.innerText = newStatus;
    }

    function replaceActionsWithEdit(id) {
        const actionsCell = document.getElementById(`actions-${id}`);
        if (!actionsCell) return;
        actionsCell.innerHTML = `<button onclick="openEditModal(${id})" class="bg-gray-500 hover:bg-gray-600 text-white text-sm px-3 py-1 rounded-lg shadow transition">✏️ Edit</button>`;
    }

    // Setujui via AJAX (POST)
    function setujuiPeminjaman(id) {
        fetch(`/admin/barang/peminjaman/${id}/setujui`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // update row status & actions and update stats
                applyStatusToRow(id, data.new_status || 'disetujui');
                replaceActionsWithEdit(id);
                updateStatsUI(data.counts);
            } else {
                console.error('Gagal:', data);
            }
        })
        .catch(err => console.error(err));
    }

    // Tolak via AJAX (POST)
    function tolakPeminjaman(id) {
        fetch(`/admin/barang/peminjaman/${id}/tolak`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                applyStatusToRow(id, data.new_status || 'ditolak');
                replaceActionsWithEdit(id);
                updateStatsUI(data.counts);
            } else {
                console.error('Gagal:', data);
            }
        })
        .catch(err => console.error(err));
    }

    // --- Modal handling for "Edit" action ---
    function openEditModal(id) {
        currentEditId = id;
        const modal = document.getElementById('editModal');
        const info = document.getElementById('modal-info');

        // fill modal info (nama barang + peminjam) if available from row
        const row = document.getElementById(`row-${id}`);
        if (row) {
            const nama = row.querySelector('td:nth-child(2)')?.innerText || '';
            const peminjam = row.querySelector('td:nth-child(3)')?.innerText || '';
            info.innerText = `Pengajuan: ${nama} — Pemohon: ${peminjam}`;
        } else {
            info.innerText = 'Ubah status peminjaman';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // bind modal buttons
        document.getElementById('modal-approve-btn').onclick = function() {
            // call approve and close modal
            setujuiPeminjaman(currentEditId);
            closeEditModal();
        };
        document.getElementById('modal-reject-btn').onclick = function() {
            tolakPeminjaman(currentEditId);
            closeEditModal();
        };
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentEditId = null;
    }

    // close modal on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('editModal');
            if (!modal.classList.contains('hidden')) closeEditModal();
        }
    });
</script>
@endsection
