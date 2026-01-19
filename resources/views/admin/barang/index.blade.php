@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
<style>
  .glass {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
  .hover-glow {
    transition: all .3s ease;
  }
  .hover-glow:hover {
    box-shadow: 0 0 20px rgba(168, 85, 247, .45);
    transform: translateY(-3px);
  }
</style>

<div class="min-h-screen bg-gradient-to-br from-indigo-950 via-purple-900 to-indigo-900 text-white p-8 rounded-2xl">

  <div class="flex justify-between items-center mb-8">
    <h2 class="text-3xl font-bold">📦 Data Barang</h2>
    <a href="{{ route('admin.barang.create') }}" 
       class="bg-purple-600 hover:bg-purple-500 px-4 py-2 rounded-lg font-semibold shadow hover-glow">
      ➕ Tambah Barang
    </a>
  </div>

  @if(session('success'))
    <div class="mb-4 p-3 rounded-lg bg-green-600/30 border border-green-500 text-green-200">
      {{ session('success') }}
    </div>
  @endif

  <div class="glass p-5 rounded-2xl overflow-x-auto shadow-md hover-glow">
    <table class="min-w-full text-sm text-white">
      <thead>
        <tr class="text-left text-xs text-purple-300 border-b border-white/20">
          <th class="py-3 px-3">#</th>
          <th class="py-3 px-3">Foto</th>
          <th class="py-3 px-3">Kode</th>
          <th class="py-3 px-3">Nama</th>
          <th class="py-3 px-3">Kategori</th>
          <th class="py-3 px-3">Jumlah</th>
          <th class="py-3 px-3">Kondisi</th>
          <th class="py-3 px-3">Lokasi</th>
          <th class="py-3 px-3">Status</th>
          <th class="py-3 px-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($barangs as $i => $b)
          <tr class="border-b border-white/10 hover:bg-white/10 transition">
            <td class="py-3 px-3">{{ $i + 1 }}</td>

            <td class="py-3 px-3">
              @if($b->foto)
                <img src="{{ asset('storage/' . $b->foto) }}" 
                     alt="{{ $b->nama_barang }}" 
                     class="w-12 h-12 object-cover rounded-lg border border-white/10">
              @else
                <span class="text-xs text-gray-400 italic">Belum ada foto</span>
              @endif
            </td>

            <td class="py-3 px-3">{{ $b->kode_barang }}</td>
            <td class="py-3 px-3 font-semibold">{{ $b->nama_barang }}</td>
            <td class="py-3 px-3">{{ $b->kategori ?? '-' }}</td>
            <td class="py-3 px-3">{{ $b->jumlah }}</td>
            <td class="py-3 px-3">{{ $b->kondisi }}</td>
            <td class="py-3 px-3">{{ $b->lokasi ?? '-' }}</td>
            <td class="py-3 px-3 capitalize">{{ $b->status }}</td>

            <td class="py-3 px-3 text-center flex justify-center gap-2">
              <a href="{{ route('admin.barang.edit', $b->id) }}" 
                 class="px-3 py-1 bg-blue-500 hover:bg-blue-400 rounded-md text-sm">✏️ Edit</a>

              <form action="{{ route('admin.barang.destroy', $b->id) }}" method="POST" 
                    onsubmit="return confirm('Hapus barang {{ $b->nama_barang }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="px-3 py-1 bg-rose-500 hover:bg-rose-400 rounded-md text-sm">
                  🗑 Hapus
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="10" class="py-6 text-center text-purple-300">Belum ada data barang.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
