@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-lg rounded-2xl p-8">
    <h1 class="text-2xl font-bold text-purple-800 mb-6 flex items-center">
        👤 <span class="ml-2">Ubah Profil</span>
    </h1>

    @if(session('status'))
        <div class="bg-green-100 border-l-4 border-green-600 text-green-800 p-3 rounded mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm" class="space-y-6">
        @csrf

        <!-- Foto Profil -->
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
            <div>
                @if($user->profile_picture)
                    <img id="preview" src="{{ asset('storage/profile_pictures/'.$user->profile_picture) }}"
                         class="w-28 h-28 rounded-full border-4 border-purple-400 shadow-lg object-cover transition-all duration-300">
                @else
                    <div id="preview"
                         class="w-28 h-28 rounded-full bg-purple-300 flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ganti Foto Profil</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*"
                       class="block w-full text-sm text-gray-600 border border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-2 cursor-pointer"
                       onchange="previewImage(event)">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maks: 2MB</p>
            </div>
        </div>

        <!-- Nama -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-2">
        </div>

        <!-- username -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input type="text" name="username" value="{{ old('username', $user->username) }}"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-2">
        </div>

        <!-- Username Telegram -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Username Telegram</label>
            <div class="flex items-center">
                <span class="text-gray-500 mr-2">@</span>
                <input type="text" name="telegram_username"
                    value="{{ old('telegram_username', $user->telegram_username) }}"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-2">
            </div>
        </div>

        <!-- Role dan Status -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
            <div>
                <p class="text-gray-600 font-semibold">Role:</p>
                <p class="text-purple-700 font-bold capitalize">{{ $user->role }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-semibold">Status Akun:</p>
                <span class="px-3 py-1 rounded-full text-sm font-semibold
                    {{ $user->status === 'approved' ? 'bg-green-100 text-green-700' :
                       ($user->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                    {{ ucfirst($user->status) }}
                </span>
            </div>
        </div>

        <!-- Tombol Simpan & Kembali -->
        <div class="flex justify-end mt-6 gap-3">
            <!-- Tombol Kembali -->
            <button type="button"
                onclick="window.history.back()"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-6 py-2 rounded-lg shadow transition">
                ⬅️ Kembali
            </button>

            <!-- Tombol Simpan -->
            <button type="submit"
                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition">
                💾 Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<!-- Modal Cropper -->
<div id="cropper-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-md">
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-[90vw] sm:w-[500px] text-center animate-fade-in">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Sesuaikan Foto Profil</h2>
        <div class="flex justify-center items-center max-h-[70vh] overflow-hidden">
            <img id="cropper-image" class="max-w-full max-h-[70vh] object-contain">
        </div>
        <div class="flex justify-end mt-5 gap-3">
            <button onclick="cancelCrop()" class="px-4 py-2 rounded-lg border text-gray-700 hover:bg-gray-100">Batal</button>
            <button onclick="uploadCroppedImage()" class="px-5 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700 shadow">Simpan</button>
        </div>
    </div>
</div>

<!-- CropperJS -->
<link href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.2/dist/cropper.min.js"></script>

<script>
    let cropper;

    function previewImage(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            const img = document.getElementById('cropper-image');
            img.src = e.target.result;

            document.getElementById('cropper-modal').classList.remove('hidden');

            img.onload = () => {
                if (cropper) cropper.destroy();
                cropper = new Cropper(img, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    responsive: true,
                    background: false,
                });
            };
        };
        reader.readAsDataURL(file);
    }

    function cancelCrop() {
        document.getElementById('cropper-modal').classList.add('hidden');
        if (cropper) cropper.destroy();
    }

    function uploadCroppedImage() {
        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400,
            imageSmoothingQuality: 'high'
        });

        canvas.toBlob((blob) => {
            const formData = new FormData();
            formData.append('cropped_image', blob, 'profile.jpg');

            fetch("{{ route('profile.upload-cropped') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('preview').src = data.url + '?t=' + new Date().getTime();
                    document.getElementById('cropper-modal').classList.add('hidden');
                    alert("✅ Foto profil berhasil diperbarui!");
                } else {
                    alert("❌ Gagal menyimpan foto. Coba lagi.");
                }
            })
            .catch(() => alert("❌ Terjadi kesalahan. Silakan coba lagi."));
        }, 'image/jpeg');
    }
</script>

<style>
    #cropper-image {
        max-width: 90vw;
        max-height: 70vh;
        display: block;
        margin: 0 auto;
    }
    .cropper-view-box, .cropper-face {
        border-radius: 50% !important;
    }
    #cropper-modal {
        backdrop-filter: blur(8px);
        background-color: rgba(0, 0, 0, 0.4);
    }
    @keyframes fade-in {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
</style>
@endsection
