@extends('layouts.user')

@section('content')
<div class="max-w-3xl mx-auto bg-white shadow-lg rounded-2xl p-8">
    <h1 class="text-2xl font-bold text-purple-800 mb-6 flex items-center">
        👤 <span class="ml-2">Profil Super Admin</span>
    </h1>

    @if(session('status'))
        <div class="bg-green-100 border-l-4 border-green-600 text-green-800 p-3 rounded mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('superadmin.profile.update') }}" method="POST" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Username Telegram</label>
            <div class="flex items-center">
                <span class="text-gray-500 mr-2">@</span>
                <input type="text" name="telegram_username" value="{{ old('telegram_username', $user->telegram_username) }}"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-purple-500 focus:border-purple-500 p-2">
            </div>
            <p class="text-xs text-gray-500 mt-1">Pastikan username sama persis dengan yang ada di Telegram.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
            <div>
                <p class="text-gray-600 font-semibold">Role:</p>
                <p class="text-purple-700 font-bold">{{ ucfirst($user->role) }}</p>
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

        <div class="flex justify-end mt-6">
            <button type="submit"
                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-2 rounded-lg shadow transition">
                💾 Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
