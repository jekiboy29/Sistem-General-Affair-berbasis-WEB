<div class="space-y-6">
  <div class="flex justify-between items-center">
    <h3 class="font-semibold text-gray-700 text-lg">💸 Transaksi Pembelian</h3>
    <a href="{{ route('transaksi.create') }}" 
       class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition">
       + Tambah Transaksi
    </a>
  </div>

  <div class="glass rounded-2xl p-4 overflow-x-auto">
    @if (session('success'))
      <div class="mb-3 text-green-600 font-medium">
        ✅ {{ session('success') }}
      </div>
    @endif

    <table class="w-full text-sm text-gray-600">
      <thead>
        <tr class="text-left border-b border-gray-200">
          <th class="py-2">Nama Pembelian</th>
          <th>Quantity</th>
          <th>Harga</th>
          <th>Struk</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($transactions as $item)
          <tr class="border-b">
            <td class="py-2">{{ $item->nama_pembelian }}</td>
            <td>{{ $item->qty }}</td>
            <td>Rp{{ number_format($item->harga, 0, ',', '.') }}</td>
            <td>
              @if ($item->struk)
                <img src="{{ asset('storage/' . $item->struk) }}" 
                     class="w-10 h-10 rounded-lg object-cover cursor-pointer" 
                     onclick="showStruk('{{ asset('storage/' . $item->struk) }}')">
              @else
                <span class="text-gray-400 italic">Tidak ada</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-center py-3 text-gray-500">Belum ada transaksi</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Modal preview struk -->
<div id="strukModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50">
  <img id="strukPreview" src="" class="max-w-[90%] max-h-[90%] rounded-lg shadow-lg">
</div>

<script>
function showStruk(url) {
  const modal = document.getElementById('strukModal');
  const img = document.getElementById('strukPreview');
  img.src = url;
  modal.classList.remove('hidden');
}
document.getElementById('strukModal').onclick = () => {
  document.getElementById('strukModal').classList.add('hidden');
};
</script>
