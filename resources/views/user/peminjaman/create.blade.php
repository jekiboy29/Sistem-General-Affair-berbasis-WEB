@extends('layouts.user')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-lg rounded-2xl p-6 md:p-8 animate-fadeIn">
    <h1 class="text-2xl md:text-3xl font-bold text-purple-800 mb-6 flex items-center">
        📄 <span class="ml-2">Ajukan Peminjaman</span>
    </h1>

    @if (session('error'))
        <div class="bg-red-500 text-white px-4 py-2 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('user.peminjaman.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Pilih Barang -->
        <div>
            <label for="barang_id" class="block font-semibold text-gray-700 mb-2">
                Pilih Barang <span class="text-red-500">*</span>
            </label>
            <select name="barang_id" id="barang_id"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                <option value="">-- Pilih Barang --</option>
                @foreach ($barangs as $item)
                    <option 
                        value="{{ $item->id }}" 
                        data-stok="{{ max(0, $item->jumlah - $item->jumlah_rusak - \App\Models\Peminjaman::where('barang_id', $item->id)->whereIn('status', ['dipinjam', 'disetujui'])->sum('jumlah_pinjam')) }}"
                        {{ $selectedBarangId == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_barang }} (Kode: {{ $item->kode_barang }})
                    </option>
                @endforeach
            </select>
            @error('barang_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror

            <!-- Info stok -->
            <p id="stok-info" class="text-sm text-gray-600 mt-2 hidden"></p>
        </div>

        <!-- Jumlah Pinjam -->
        <div>
            <label for="jumlah_pinjam" class="block font-semibold text-gray-700 mb-2">
                Jumlah Barang yang Dipinjam <span class="text-red-500">*</span>
            </label>
            <input type="number" name="jumlah_pinjam" id="jumlah_pinjam" min="1" value="{{ old('jumlah_pinjam', 1) }}"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
            @error('jumlah_pinjam')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tanggal Pinjam -->
        <div>
            <label for="tanggal_pinjam" class="block font-semibold text-gray-700 mb-2">
                Tanggal Pinjam <span class="text-red-500">*</span>
            </label>
            <input type="date" name="tanggal_pinjam" id="tanggal_pinjam"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
            @error('tanggal_pinjam')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tanggal Kembali -->
        <div>
            <label for="tanggal_kembali" class="block font-semibold text-gray-700 mb-2">
                Tanggal Kembali
            </label>
            <input type="date" name="tanggal_kembali" id="tanggal_kembali"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
            @error('tanggal_kembali')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tujuan -->
        <div>
            <label for="tujuan" class="block font-semibold text-gray-700 mb-2">
                Tujuan / Keperluan
            </label>
            <textarea name="tujuan" id="tujuan" rows="3"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition resize-none"
                placeholder="Tuliskan keperluan peminjaman..."></textarea>
            @error('tujuan')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tombol -->
        <div class="flex justify-between items-center pt-4">
            <a href="{{ route('user.peminjaman.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition">
                ← Kembali
            </a>
            <button type="submit"
                class="inline-flex items-center px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow transition">
                🚀 Ajukan Sekarang
            </button>
        </div>
    </form>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
    animation: fadeIn 0.4s ease-in-out;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const barangSelect = document.getElementById("barang_id");
    const stokInfo = document.getElementById("stok-info");
    const jumlahInput = document.getElementById("jumlah_pinjam");

    function updateStokInfo() {
        const selectedOption = barangSelect.options[barangSelect.selectedIndex];
        const stok = selectedOption.dataset.stok;

        if (stok !== undefined && stok !== "") {
            stokInfo.classList.remove("hidden");
            stokInfo.textContent = `Stok tersedia: ${stok} unit`;

            jumlahInput.max = stok;
        } else {
            stokInfo.classList.add("hidden");
            stokInfo.textContent = "";
            jumlahInput.removeAttribute("max");
        }
    }

    barangSelect.addEventListener("change", updateStokInfo);
    updateStokInfo();
});
</script>
@endsection
