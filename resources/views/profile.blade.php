@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<style>
  .neon-bg {
    background: linear-gradient(135deg, #2E026D, #6D28D9, #9333EA);
    min-height: 100vh;
    color: #fff;
  }
  .glass {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
  .hover-glow { transition: all .3s ease; }
  .hover-glow:hover { box-shadow: 0 0 20px rgba(168, 85, 247, .45); transform: translateY(-3px); }
  .avatar {
    width: 110px; height: 110px; border-radius: 50%;
    object-fit: cover; border: 3px solid #a855f7;
  }
  .avatar-generated {
    width: 110px; height: 110px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 48px; font-weight: bold; color: white;
    border: 3px solid #a855f7;
  }
</style>

@php
  $colors = ['#9333EA', '#6D28D9', '#7C3AED', '#5B21B6', '#A855F7', '#C084FC'];
  $colorIndex = crc32(strtolower($user->name)) % count($colors);
  $avatarColor = $colors[$colorIndex];
  $initial = strtoupper(substr($user->name, 0, 1));
@endphp

<div class="neon-bg p-8 rounded-2xl fade-in">
  <h2 class="text-3xl font-bold mb-6">👤 Profil Saya</h2>

  @if(session('status'))
    <div class="bg-green-100 border-l-4 border-green-600 text-green-800 p-3 rounded mb-4">
        {{ session('status') }}
    </div>
  @endif

  <div class="glass p-6 rounded-2xl shadow-lg max-w-3xl mx-auto">
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div class="flex flex-col items-center mb-6">
        @if($user->profile_picture)
          <img src="{{ asset('storage/profile_pictures/'.$user->profile_picture) }}" alt="Profile Picture" class="avatar mb-3">
          <form action="{{ route('profile.deletePhoto') }}" method="POST" onsubmit="return confirm('Hapus foto profil ini?')">
            @csrf
            <button type="submit" class="text-sm bg-rose-600 hover:bg-rose-500 text-white px-3 py-1 rounded-md mb-3">
              🗑 Hapus Foto Profil
            </button>
          </form>
        @else
          <div class="avatar-generated mb-3" style="background-color: {{ $avatarColor }}">
            {{ $initial }}
          </div>
        @endif

        <label class="block text-sm text-purple-300 mb-2">Ganti Foto Profil</label>
        <input type="file" name="profile_picture" accept="image/*" class="text-sm text-white">
        @error('profile_picture')
          <div class="text-red-300 text-sm mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm text-purple-300 mb-2">Nama</label>
          <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full p-2 rounded-md bg-purple-900/50 text-white border border-purple-500 focus:ring-2 focus:ring-purple-400">
          @error('name')<div class="text-red-300 text-sm mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
          <label class="block text-sm text-purple-300 mb-2">Username</label>
          <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full p-2 rounded-md bg-purple-900/50 text-white border border-purple-500 focus:ring-2 focus:ring-purple-400">
          @error('username')<div class="text-red-300 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="mt-5">
        <label class="block text-sm text-purple-300 mb-2">Username Telegram</label>
        <input type="text" name="telegram_username" value="{{ old('telegram_username', $user->telegram_username) }}" class="w-full p-2 rounded-md bg-purple-900/50 text-white border border-purple-500 focus:ring-2 focus:ring-purple-400">
        @error('telegram_username')<div class="text-red-300 text-sm mt-1">{{ $message }}</div>@enderror
      </div>

      <div class="mt-8 text-center">
        <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white px-5 py-2 rounded-md hover-glow">
          💾 Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
