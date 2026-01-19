@extends('layouts.app')

@section('title', 'Dashboard Super Admin')

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
  .hover-glow {
    transition: all .3s ease;
  }
  .hover-glow:hover {
    box-shadow: 0 0 20px rgba(168, 85, 247, .45);
    transform: translateY(-3px);
  }
  .fade-in {
    animation: fadeIn 0.7s ease forwards;
    opacity: 0;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
  }

  select {
    background-color: rgba(88, 28, 135, 0.8);
    color: #fff;
    border: 1px solid rgba(168, 85, 247, 0.7);
    border-radius: 0.5rem;
    padding: 0.25rem 0.75rem;
    transition: all 0.25s ease;
    appearance: none;
    cursor: pointer;
  }
  select:hover {
    background-color: rgba(147, 51, 234, 0.8);
    box-shadow: 0 0 10px rgba(168, 85, 247, 0.4);
  }
  select:focus {
    outline: none;
    box-shadow: 0 0 12px rgba(168, 85, 247, 0.8);
  }
  option {
    background-color: #2E026D;
    color: #fff;
  }
</style>

<div class="neon-bg p-8 fade-in rounded-2xl shadow-xl">

  {{-- HEADER --}}
  <div class="flex items-center justify-between mb-10">
    <div>
      <h2 class="text-3xl font-bold">Dashboard Super Admin</h2>
      <p class="text-purple-200 text-sm">Kelola persetujuan akun & peran pengguna.</p>
    </div>
    <div class="flex items-center gap-3 glass px-4 py-2 rounded-xl shadow-md">
      <div class="text-right">
        <div class="text-xs text-purple-300">Hai,</div>
        <div class="font-semibold text-white">{{ auth()->user()->name ?? 'Super Admin' }}</div>
      </div>
    </div>
  </div>

  @if(session('status'))
    <div class="bg-green-100 border-l-4 border-green-600 text-green-800 p-3 rounded mb-4">
        {{ session('status') }}
    </div>
@endif

  {{-- STAT CARDS --}}
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
    <div class="p-5 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-lg hover-glow">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-xs uppercase opacity-90">Total Akun</div>
          <div class="text-3xl font-bold mt-2">{{ $counts['total'] ?? 0 }}</div>
        </div>
        <div class="text-4xl opacity-80">👥</div>
      </div>
    </div>

    <div class="p-5 rounded-2xl bg-gradient-to-br from-yellow-400 to-orange-500 text-white shadow-lg hover-glow">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-xs uppercase opacity-90">Pending</div>
          <div class="text-3xl font-bold mt-2">{{ $counts['pending'] ?? 0 }}</div>
        </div>
        <div class="text-4xl opacity-80">⏳</div>
      </div>
    </div>

    <div class="p-5 rounded-2xl bg-gradient-to-br from-green-400 to-teal-500 text-white shadow-lg hover-glow">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-xs uppercase opacity-90">Approved</div>
          <div class="text-3xl font-bold mt-2">{{ $counts['approved'] ?? 0 }}</div>
        </div>
        <div class="text-4xl opacity-80">✅</div>
      </div>
    </div>

    <div class="p-5 rounded-2xl bg-gradient-to-br from-rose-400 to-pink-500 text-white shadow-lg hover-glow">
      <div class="flex items-center justify-between">
        <div>
          <div class="text-xs uppercase opacity-90">Rejected</div>
          <div class="text-3xl font-bold mt-2">{{ $counts['rejected'] ?? 0 }}</div>
        </div>
        <div class="text-4xl opacity-80">❌</div>
      </div>
    </div>
  </div>

  {{-- PENDING USERS --}}
  <div class="mb-10 fade-in">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-xl">🕓 Akun Pending</h3>
      <span class="text-sm text-purple-200">Approve atau reject akun yang menunggu.</span>
    </div>

    <div class="glass p-5 rounded-2xl overflow-x-auto shadow-md hover-glow">
      <table class="min-w-full text-sm text-white">
        <thead>
          <tr class="text-left text-xs text-purple-300 border-b border-white/20">
            <th class="py-3 px-3">#</th>
            <th class="py-3 px-3">Nama</th>
            <th class="py-3 px-3">Username</th>
            <th class="py-3 px-3">Telegram</th>
            <th class="py-3 px-3">Role</th>
            <th class="py-3 px-3 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($pendingUsers as $u)
            <tr class="border-b border-white/10 hover:bg-white/10 transition">
              <td class="py-3 px-3">{{ $loop->iteration }}</td>
              <td class="py-3 px-3">{{ $u->name }}</td>
              <td class="py-3 px-3">{{ $u->username }}</td>
              <td class="py-3 px-3">
                {{ $u->telegram_username ? '@' . $u->telegram_username : '-' }}
              </td>
              <td class="py-3 px-3">{{ ucfirst($u->role) }}</td>
              <td class="py-3 px-3 flex justify-center gap-2">
                <form action="{{ route('superadmin.users.approve', $u->id) }}" method="POST" onsubmit="return confirm('Approve akun {{ $u->username }}?')">
                  @csrf
                  <button type="submit" class="px-3 py-1 rounded-md bg-green-500 hover:bg-green-400 text-sm text-white shadow">Approve</button>
                </form>
                <form action="{{ route('superadmin.users.reject', $u->id) }}" method="POST" onsubmit="return confirm('Reject akun {{ $u->username }}?')">
                  @csrf
                  <button type="submit" class="px-3 py-1 rounded-md bg-rose-500 hover:bg-rose-400 text-sm text-white shadow">Reject</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="py-6 text-center text-purple-300">✨ Tidak ada akun pending.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- SEMUA AKUN --}}
  <div class="fade-in">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-semibold text-xl">👥 Semua Akun</h3>
      <span class="text-sm text-purple-200">Ubah role atau hapus akun.</span>
    </div>

    <div class="glass p-5 rounded-2xl overflow-x-auto shadow-md hover-glow">
      <table class="min-w-full text-sm text-white">
        <thead>
          <tr class="text-left text-xs text-purple-300 border-b border-white/20">
            <th class="py-3 px-3">#</th>
            <th class="py-3 px-3">Nama</th>
            <th class="py-3 px-3">Username</th>
            <th class="py-3 px-3">Telegram</th>
            <th class="py-3 px-3">Role</th>
            <th class="py-3 px-3">Status</th>
            <th class="py-3 px-3 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($approvedUsers as $i => $u)
            <tr class="border-b border-white/10 hover:bg-white/10 transition">
              <td class="py-3 px-3">{{ $i + 1 }}</td>
              <td class="py-3 px-3">{{ $u->name }}</td>
              <td class="py-3 px-3">{{ $u->username }}</td>
              <td class="py-3 px-3">
                {{ $u->telegram_username ? '@' . $u->telegram_username : '-' }}
              </td>
              <td class="py-3 px-3">
                <form action="{{ route('superadmin.users.updateRole', $u->id) }}" method="POST">
                  @csrf
                  <select name="role" onchange="this.form.submit()">
                    <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ $u->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                  </select>
                </form>
              </td>
              <td class="py-3 px-3 capitalize">{{ $u->status }}</td>
              <td class="py-3 px-3 text-center">
                <form action="{{ route('superadmin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Hapus akun {{ $u->username }}?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="px-3 py-1 rounded-md bg-gray-700 hover:bg-gray-600 text-sm text-white shadow">🗑 Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="py-6 text-center text-purple-300">Belum ada pengguna terdaftar.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
