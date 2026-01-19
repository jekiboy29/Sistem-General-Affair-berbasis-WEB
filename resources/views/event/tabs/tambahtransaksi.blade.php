<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Transaksi</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

  <div class="w-full max-w-lg bg-white p-6 rounded-xl shadow-lg" >
    <h2 class="text-2xl font-semibold mb-4 text-gray-800 text-center">Tambah Transaksi</h2>

    <!-- 🔹 Form Tambah Transaksi -->
    <form method="POST" action="{{ route('transaksi.store') }}" enctype="multipart/form-data" class="space-y-4">
      @csrf

      <div>
        <label class="block text-sm font-medium mb-1">Nama Pembelian</label>
        <input type="text" name="nama_pembelian" class="w-full border rounded-lg p-2 focus:ring-amber-400 focus:border-amber-400" required>
      </div>

      <div class="flex gap-3">
        <div class="flex-1">
          <label class="block text-sm font-medium mb-1">Quantity</label>
          <input type="text" name="qty" class="w-full border rounded-lg p-2 focus:ring-amber-400 focus:border-amber-400" required>
        </div>
        <div class="flex-1">
          <label class="block text-sm font-medium mb-1">Harga</label>
          <input type="number" name="harga" class="w-full border rounded-lg p-2 focus:ring-amber-400 focus:border-amber-400" required>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Upload Struk</label>
        <input type="file" name="struk" id="strukInput" class="w-full border rounded-lg p-2 bg-gray-50" accept="image/*">
      </div>

      <!-- 🔸 Preview Struk -->
      <div id="previewContainer" class="hidden mt-3">
        <p class="text-sm text-gray-500 mb-1">Preview Struk:</p>
        <img id="previewImage" src="" alt="Preview Struk" class="rounded-lg shadow-md max-h-48">
      </div>

      <div class="flex justify-end gap-3 pt-3">
        <a href="{{ route('transaksi.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</a>
        <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600">Simpan</button>
      </div>
    </form>
  </div>

  <!-- 🔸 Script Preview Gambar -->
  <script>
    document.getElementById('strukInput').addEventListener('change', function(event) {
      const file = event.target.files[0];
      const previewContainer = document.getElementById('previewContainer');
      const previewImage = document.getElementById('previewImage');

      if (file) {
        const reader = new FileReader();
        reader.onload = e => {
          previewImage.src = e.target.result;
          previewContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
      } else {
        previewContainer.classList.add('hidden');
      }
    });
  </script>

</body>
</html>
