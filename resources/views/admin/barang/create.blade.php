@extends('layouts.admin')
@section('title', 'Tambah Barang')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-950 via-purple-900 to-indigo-900 text-white p-8 rounded-2xl">

  <h2 class="text-3xl font-bold mb-6">➕ Tambah Barang Baru</h2>

  <form id="barangForm" action="{{ route('admin.barang.store') }}" method="POST" enctype="multipart/form-data" class="glass p-6 rounded-2xl max-w-2xl bg-white text-gray-800">
    @csrf

    <div class="mb-4">
      <label class="block text-sm mb-1">Kode Barang</label>
      <input type="text" name="kode_barang" value="{{ old('kode_barang') }}" required
             class="w-full px-3 py-2 rounded bg-white/90 border border-purple-400 focus:outline-none">
    </div>

    <div class="mb-4">
      <label class="block text-sm mb-1">Nama Barang</label>
      <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" required
             class="w-full px-3 py-2 rounded bg-white/90 border border-purple-400 focus:outline-none">
    </div>

    <div class="mb-4">
      <label class="block text-sm mb-1">Kategori</label>
      <input type="text" name="kategori" value="{{ old('kategori') }}"
             class="w-full px-3 py-2 rounded bg-white/90 border border-purple-400 focus:outline-none">
    </div>

    <div class="mb-4">
      <label class="block text-sm mb-1">Jumlah</label>
      <input type="number" name="jumlah" value="{{ old('jumlah', 1) }}" min="1" required
             class="w-full px-3 py-2 rounded bg-white/90 border border-purple-400 focus:outline-none">
    </div>

    <div class="mb-4">
      <label class="block text-sm mb-1">Kondisi</label>
      <input type="text" name="kondisi" value="{{ old('kondisi', 'Baik') }}" required
             class="w-full px-3 py-2 rounded bg-white/90 border border-purple-400 focus:outline-none">
    </div>

    <div class="mb-4">
      <label class="block text-sm mb-1">Lokasi</label>
      <input type="text" name="lokasi" value="{{ old('lokasi') }}"
             class="w-full px-3 py-2 rounded bg-white/90 border border-purple-400 focus:outline-none">
    </div>

    {{-- Status (Fixed ke “Tersedia”) --}}
    <div class="mb-4">
      <label class="block text-sm mb-1">Status</label>
      <select name="status_disabled" disabled
        class="w-full px-3 py-2 rounded bg-gray-200 border border-purple-400 text-gray-600 cursor-not-allowed">
        <option value="tersedia" selected>Tersedia</option>
      </select>
      <input type="hidden" name="status" value="tersedia">
    </div>

    {{-- Upload Foto --}}
    <div class="mb-4">
      <label class="block text-sm mb-1">Foto Barang</label>
      <div class="flex gap-3 items-center">
        <button type="button" id="btnSelectImage" class="bg-purple-600 text-white px-4 py-2 rounded-lg shadow hover:bg-purple-700">Pilih Foto</button>
        <span id="selectedFilename" class="text-sm text-gray-600"></span>
      </div>

      <input type="file" id="inputImage" accept="image/*" class="hidden">
      <input type="file" id="croppedImageInput" name="cropped_image" class="hidden">
    </div>

    <div class="flex gap-3 mt-6">
      <button type="submit" class="bg-purple-600 hover:bg-purple-500 px-5 py-2 rounded-lg font-semibold text-white">
        💾 Simpan
      </button>
      <a href="{{ route('admin.barang.dashboard') }}" class="bg-gray-700 hover:bg-gray-600 px-5 py-2 rounded-lg text-white">
        ❌ Batal
      </a>
    </div>
  </form>
</div>

<!-- Modal cropper -->
<div id="cropperModal" class="fixed inset-0 bg-black/60 hidden z-50 items-center justify-center p-4">
  <div class="bg-white rounded-lg overflow-hidden max-w-xl w-full">
    <div class="p-3 border-b flex justify-between items-center">
      <h3 class="font-semibold">Crop Foto Barang (1:1)</h3>
      <button id="closeCropper" class="text-gray-600 hover:text-gray-900">&times;</button>
    </div>
    <div class="p-4">
      <div class="w-full h-96 bg-gray-100 flex items-center justify-center">
        <img id="imagePreview" style="max-width:100%; max-height:100%; display:block;">
      </div>

      <div class="mt-4 flex items-center gap-3">
        <label class="flex items-center gap-2">
          Zoom:
          <input id="zoomRange" type="range" min="0.5" max="3" step="0.01" value="1" class="w-48">
        </label>

        <button id="applyCrop" class="ml-auto bg-purple-600 text-white px-4 py-2 rounded">Simpan Foto</button>
      </div>
    </div>
  </div>
</div>

<!-- Cropper.js CDN -->
<link  href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const inputImage = document.getElementById('inputImage');
  const btnSelect = document.getElementById('btnSelectImage');
  const modal = document.getElementById('cropperModal');
  const imagePreview = document.getElementById('imagePreview');
  const closeCropper = document.getElementById('closeCropper');
  const applyCrop = document.getElementById('applyCrop');
  const zoomRange = document.getElementById('zoomRange');
  const selectedFilename = document.getElementById('selectedFilename');
  const croppedImageInput = document.getElementById('croppedImageInput');

  let cropper = null;
  let currentFile = null;

  btnSelect.addEventListener('click', () => inputImage.click());
  inputImage.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;
    selectedFilename.textContent = file.name;
    currentFile = file;

    const url = URL.createObjectURL(file);
    imagePreview.src = url;
    // open modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // destroy existing cropper
    if (cropper) {
      cropper.destroy();
      cropper = null;
    }

    // create cropper after image loaded
    imagePreview.onload = function () {
      cropper = new Cropper(imagePreview, {
        aspectRatio: 1,
        viewMode: 1,
        autoCropArea: 1,
        movable: true,
        zoomable: true,
        scalable: false,
        background: false,
        ready() {
          // sync initial zoom range
          zoomRange.value = cropper.getData().scaleX || 1;
        }
      });
    };
  });

  closeCropper.addEventListener('click', () => {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    if (cropper) {
      cropper.destroy(); cropper = null;
    }
    inputImage.value = '';
    selectedFilename.textContent = '';
  });

  zoomRange.addEventListener('input', (e) => {
    if (!cropper) return;
    const val = parseFloat(e.target.value);
    cropper.zoomTo(val);
  });

  applyCrop.addEventListener('click', async () => {
    if (!cropper) return;

    const canvas = cropper.getCroppedCanvas({
      width: 800, // ukuran output foto persegi (bisa disesuaikan)
      height: 800,
      imageSmoothingQuality: 'high'
    });

    // convert to blob then create a File to inject into form
    canvas.toBlob(function(blob) {
      const file = new File([blob], 'barang_' + Date.now() + '.png', { type: 'image/png' });

      // set file into hidden input (croppedImageInput)
      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(file);
      croppedImageInput.files = dataTransfer.files;

      // update preview name
      selectedFilename.textContent = file.name;

      // close modal
      modal.classList.add('hidden');
      modal.classList.remove('flex');

      // destroy cropper
      cropper.destroy();
      cropper = null;
      inputImage.value = '';
    }, 'image/png', 0.9);
  });

  // optional: handle form submit to show spinner / disable button
  document.getElementById('barangForm').addEventListener('submit', function() {
    // allow normal submit, croppedImageInput will be sent
  });
});
</script>

@endsection
