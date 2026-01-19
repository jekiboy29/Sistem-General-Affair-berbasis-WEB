@extends('layouts.app')

@section('title', 'Verifikasi Pendaftaran')

@section('content')
<div class="bg-white shadow-md rounded-xl p-6">
  <h2 class="text-2xl font-bold mb-6">Verifikasi Pendaftaran Akun Baru</h2>

  @if(session('status'))
    <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">
      {{ session('status') }}
    </div>
  @endif

  @if($pending->count())
    <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left">Nama</th>
          <th class="px-4 py-2 text-left">Email</th>
          <th class="px-4 py-2 text-left">Telegram</th>
          <th class="px-4 py-2 text-left">Role</th>
          <th class="px-4 py-2 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($pending as $user)
        <tr class="border-t hover:bg-gray-50">
          <td class="px-4 py-2">{{ $user->name }}</td>
          <td class="px-4 py-2">{{ $user->email }}</td>
          <td class="py-3 px-3">{{ '@' . $u->telegram_username }}</td>
          <td class="px-4 py-2 capitalize">{{ $user->role }}</td>
          <td class="px-4 py-2 text-center">
            <div class="flex justify-center gap-2">
              <form action="{{ route('superadmin.approve', $user->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Approve</button>
              </form>
              <form action="{{ route('superadmin.reject', $user->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Reject</button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <p class="text-gray-600">Tidak ada pendaftar baru untuk diverifikasi.</p>
  @endif
</div>
@endsection
