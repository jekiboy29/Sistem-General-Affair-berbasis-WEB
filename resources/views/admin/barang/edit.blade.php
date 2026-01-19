@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-950 via-purple-900 to-indigo-900 text-white p-8 rounded-2xl">

  <h2 class="text-3xl font-bold mb-6">✏️ Edit Barang</h2>

  <form action="{{ route('admin.barang.update', $barang->id) }}" method="POST" enctype="multipart/form-data" class="glass p-6 rounded-2xl max-w-2xl">
    @csrf
    @method('PUT')

    {{-- Foto --}}
    <div class="mb-6">
      <label class="block text-sm mb-2 font-semibold">Foto Barang</label>
      <div class="flex items-center gap-4">
        <div class="w-24 h-24 bg-white/10 rounded-lg flex items-center justify-center overflow-hidden">
          <img id="preview" 
               src="{{ $barang->foto_barang ? asset('storage/' . $barang->foto_barang) : 'https://via.placeholder.com/150?text=No+Image' }}" 
               alt="Preview" class="object-cover w-full h-full">
        </div>
        <div>
          <input type="file" id="upload" accept="image/*" class="hidden">
          <button type="button" id="chooseImageBtn" class="bg-purple-600 hover:bg-purple-500 px-4 py-2 rounded-lg font-semibold text-white transition">
            📷 Ganti Foto
          </button>
        </div>
      </div>
      <div id="cropModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-gray-900 p-6 rounded-xl max-w-md w-full text-center">
          <h3 class="text-lg font-semibold mb-3">✂️ Atur Crop Foto</h3>
          <div><img id="imageCropper" class="max-h-80 mx-auto rounded-md"></div>
          <div class="flex justify-center gap-4 mt-4">
            <button type="button" id="cropSave" class="bg-purple-600 hover:bg-purple-500 px-4 py-2 rounded-lg font-semibold">✅ Simpan</button>
            <button type="button" id="cropCancel" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg">❌ Batal</button>
          </div>
        </div>
      </div>
      <input type="file" name="cropped_image" id="cropped_image" class="hidden">
    </div>

    {{-- Data Barang --}}
    <div class="mb-4">
      <label class="block text-sm mb-1">Kode Barang</label>
      <input type="text" value="{{ $barang->kode_barang }}" disabled
             class="w-full px-3 py-2 rounded bg-white/20 border border-purple-400 cursor-not-allowed text-gray-300">
    </div>

    <div class="mb-4">
      <label class="block text-sm mb-1">Nama Barang</label>
      <input type="text" name="nama_barang" value="{{ $barang->nama_barang }}" required
             class="w-full px-3 py-2 rounded bg-white/10 border border-purple-400 text-white focus:ring-2 focus:ring-purple-500">
    </div>

    <div class="mb-4">
      <label class="block text-sm mb-1">Kategori</label>
      <input type="text" name="kategori" value="{{ $barang->kategori }}"
             class="w-full px-3 py-2 rounded bg-white/10 border border-purple-400 text-white focus:ring-2 focus:ring-purple-500">
    </div>

    <div class="mb-4">
      <label class="block text-sm mb-1">Jumlah</label>
      <input type="number" name="jumlah" value="{{ $barang->jumlah }}" min="1" required
             class="w-full px-3 py-2 rounded bg-white/10 border border-purple-400 text-white focus:ring-2 focus:ring-purple-500">
    </div>

    <div class="mb-4">
      <label class="block text-sm mb-1">Kondisi</label>
      <input type="text" name="kondisi" value="{{ $barang->kondisi }}" required
             class="w-full px-3 py-2 rounded bg-white/10 border border-purple-400 text-white focus:ring-2 focus:ring-purple-500">
    </div>

    <div class="mb-4">
      <label class="block text-sm mb-1">Lokasi</label>
      <input type="text" name="lokasi" value="{{ $barang->lokasi }}"
             class="w-full px-3 py-2 rounded bg-white/10 border border-purple-400 text-white focus:ring-2 focus:ring-purple-500">
    </div>

    {{-- Status (tidak bisa diedit) --}}
    <div class="mb-4">
      <label class="block text-sm mb-1">Status</label>
      <select name="status_disabled" disabled
        class="w-full px-3 py-2 rounded bg-gray-700/60 border border-purple-400 text-gray-300 cursor-not-allowed">
        <option value="tersedia" selected>Tersedia</option>
      </select>
      <input type="hidden" name="status" value="tersedia">
    </div>

    <div class="flex gap-3 mt-6">
      <button type="submit" class="bg-blue-600 hover:bg-blue-500 px-5 py-2 rounded-lg font-semibold">💾 Update</button>
      <a href="{{ route('admin.barang.dashboard') }}" class="bg-gray-700 hover:bg-gray-600 px-5 py-2 rounded-lg">🔙 Kembali</a>
    </div>
  </form>

  {{-- Hapus Barang --}}
  <form action="{{ route('admin.barang.destroy', $barang->id) }}" method="POST" class="inline-block mt-4"
        onsubmit="return confirm('⚠️ Yakin ingin menghapus barang ini? Data dan foto akan dihapus permanen!');">
    @csrf
    @method('DELETE')
    <button type="submit" class="bg-red-600 hover:bg-red-500 px-5 py-2 rounded-lg font-semibold">
      🗑️ Hapus Barang
    </button>
  </form>
</div>

{{-- Cropper.js --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const uploadInput = document.getElementById('upload');
  const chooseBtn = document.getElementById('chooseImageBtn');
  const cropModal = document.getElementById('cropModal');
  const imageCropper = document.getElementById('imageCropper');
  const cropSave = document.getElementById('cropSave');
  const cropCancel = document.getElementById('cropCancel');
  const preview = document.getElementById('preview');
  const croppedInput = document.getElementById('cropped_image');
  let cropper;

  chooseBtn.addEventListener('click', () => uploadInput.click());

  uploadInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (event) => {
        imageCropper.src = event.target.result;
        cropModal.classList.remove('hidden');
        cropModal.classList.add('flex');

        setTimeout(() => {
          cropper = new Cropper(imageCropper, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            background: false,
            autoCropArea: 1,
          });
        }, 100);
      };
      reader.readAsDataURL(file);
    }
  });

  cropSave.addEventListener('click', () => {
    if (cropper) {
      cropper.getCroppedCanvas({
        width: 400,
        height: 400
      }).toBlob((blob) => {
        const file = new File([blob], 'cropped_image.png', { type: 'image/png' });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        croppedInput.files = dataTransfer.files;
        preview.src = URL.createObjectURL(blob);
        cropper.destroy();
        cropper = null;
        cropModal.classList.add('hidden');
      });
    }
  });

  cropCancel.addEventListener('click', () => {
    if (cropper) {
      cropper.destroy();
      cropper = null;
    }
    cropModal.classList.add('hidden');
  });
});
</script>
@endsection
