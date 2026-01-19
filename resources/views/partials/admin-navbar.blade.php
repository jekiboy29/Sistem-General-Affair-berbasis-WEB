@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
    $colors = ['#9333EA', '#6D28D9', '#7C3AED', '#5B21B6', '#A855F7', '#C084FC'];
    $colorIndex = crc32(strtolower($user->name)) % count($colors);
    $avatarColor = $colors[$colorIndex];
    $initial = strtoupper(substr($user->name, 0, 1));
@endphp

<header class="flex items-center justify-between bg-white shadow px-4 py-3 sticky top-0 z-20">
    <div class="flex items-center space-x-2">
        <button id="menu-btn" class="md:hidden text-purple-700 text-2xl focus:outline-none" onclick="toggleSidebar()">
            ☰
        </button>
        <h1 class="text-lg font-semibold text-purple-800">Dashboard Admin</h1>
    </div>

    <div class="flex items-center space-x-3">
        <a href="{{ route('admin.profile') }}" class="flex items-center space-x-3">
            @if($user->profile_picture)
                <img src="{{ asset('storage/profile_pictures/'.$user->profile_picture) }}"
                     class="w-10 h-10 rounded-full border-2 border-purple-400 shadow-md hover:scale-105 transition">
            @else
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold border-2 border-purple-400 shadow-md hover:scale-105 transition"
                     style="background-color: {{ $avatarColor }}">
                    {{ $initial }}
                </div>
            @endif
        </a>
    </div>
</header>
