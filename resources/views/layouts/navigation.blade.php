@php
    use Illuminate\Support\Facades\Auth;

    // Cek apakah user login pakai Auth atau mode stokopname
    $isStokopname = session('stokopname_logged_in', false);

    if (Auth::check()) {
        $user = Auth::user();
        $userName = $user->name;
        $userRole = $user->role ?? 'user';
        $profilePic = $user->profile_picture ?? null;
    } elseif ($isStokopname) {
        // Mode login manual stokopname
        $user = null;
        $userName = 'Stok Opname';
        $userRole = 'event_manager';
        $profilePic = null;
    } else {
        $user = null;
        $userName = 'Tamu';
        $userRole = 'guest';
        $profilePic = null;
    }

    $colors = ['#9333EA', '#6D28D9', '#7C3AED', '#5B21B6', '#A855F7', '#C084FC'];
    $colorIndex = crc32(strtolower($userName)) % count($colors);
    $avatarColor = $colors[$colorIndex];
    $initial = strtoupper(substr($userName, 0, 1));
@endphp

<style>
@keyframes gradientShift {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
.nav-animated {
  background: linear-gradient(270deg, #7C3AED, #6D28D9, #9333EA, #5B21B6);
  background-size: 600% 600%;
  animation: gradientShift 12s ease infinite;
}
[x-cloak] { display: none !important; }
</style>

<nav x-data="{ menuOpen: false }"
    class="nav-animated shadow-lg border-b border-purple-500/30 backdrop-blur-md relative z-50">

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16 items-center">

        <!-- Logo -->
        <div class="flex items-center space-x-2">
            <img src="{{ asset('asset/favicon.png') }}" alt="Logo" class="h-8 w-8">
            <span class="text-white font-semibold text-lg tracking-wide">
                Sistem Peminjaman Sarpras
            </span>
        </div>

        <!-- ✅ Mode Normal (Auth) -->
        @if(Auth::check())
            @if($user->hasRole('super_admin'))
                <a href="{{ route('superadmin.dashboard') }}"></a>
            @elseif($user->hasRole('admin'))
                <a href="{{ route('admin.barang.index') }}"></a>
            @elseif($user->hasRole('user'))
                <a href="{{ route('user.dashboard') }}"></a>
            @endif
        @endif

        <!-- ✅ Profile Dropdown -->
        <div class="relative" x-data="{ openProfile: false }">
            <button @click="openProfile = !openProfile"
                class="flex items-center focus:outline-none hover:scale-105 transition duration-200">

                @if($profilePic)
                    <img src="{{ asset('storage/profile_pictures/'.$profilePic) }}"
                        class="w-10 h-10 rounded-full border-2 border-purple-300 shadow-md hover:shadow-purple-400/40 object-cover transition duration-200">
                @else
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold border-2 border-purple-300 shadow-md hover:shadow-purple-400/40 transition duration-200"
                        style="background-color: {{ $avatarColor }}">
                        {{ $initial }}
                    </div>
                @endif
            </button>

            <!-- Dropdown -->
            <div x-cloak
                x-show="openProfile"
                @click.away="openProfile = false"
                x-transition
                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">

                <div class="px-4 py-2 text-gray-700 border-b">
                    <span class="font-semibold">{{ $userName }}</span><br>
                    <span class="text-sm text-gray-500 capitalize">{{ $userRole }}</span>
                </div>

                @if(!$isStokopname)
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                        ✏️ <span class="ml-2">Edit Profil</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left flex items-center px-4 py-2 text-red-600 hover:bg-gray-100">
                            🚪 <span class="ml-2">Logout</span>
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('event.logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left flex items-center px-4 py-2 text-red-600 hover:bg-gray-100">
                            🚪 <span class="ml-2">Keluar</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
</nav>
