<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User - Sarpras</title>

    <!-- ✅ Tailwind via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- (Opsional) Font Inter biar halus -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.4s ease-in-out; }

        /* Animasi geser sidebar */
        @keyframes slideIn {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(-100%); opacity: 0; }
        }
        .animate-slide-in { animation: slideIn 0.3s ease forwards; }
        .animate-slide-out { animation: slideOut 0.3s ease forwards; }
    </style>
</head>
<body class="bg-gray-50 flex min-h-screen">

    @php
        use Illuminate\Support\Facades\Auth;
        $user = Auth::user();
        $colors = ['#9333EA', '#6D28D9', '#7C3AED', '#5B21B6', '#A855F7', '#C084FC'];
        $colorIndex = crc32(strtolower($user->name)) % count($colors);
        $avatarColor = $colors[$colorIndex];
        $initial = strtoupper(substr($user->name, 0, 1));
    @endphp

    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed top-0 left-0 h-screen md:static md:h-screen md:translate-x-0 transform -translate-x-full md:flex flex-col 
               bg-gradient-to-b from-purple-700 to-purple-800 text-white w-64 space-y-6 py-6 px-4 
               transition-transform duration-300 ease-in-out z-40 shadow-lg">

        <!-- Logo -->
        <div class="text-2xl font-bold text-center mb-6 select-none">
            Sarpras <span class="text-purple-200">User</span>
        </div>

        <!-- Navigasi -->
        <nav class="flex flex-col space-y-2">
            <a href="{{ route('user.peminjaman.dashboard') }}" 
               class="flex items-center px-4 py-2 rounded-lg transition duration-200 ease-in-out
                      {{ request()->routeIs('user.peminjaman.dashboard') ? 'bg-purple-600 text-white shadow-inner' : 'hover:bg-purple-600 hover:shadow-md' }}">
                🏠 <span class="ml-3 font-medium">Dashboard</span>
            </a>

            <a href="{{ route('user.peminjaman.index') }}" 
               class="flex items-center px-4 py-2 rounded-lg transition duration-200 ease-in-out
                      {{ request()->routeIs('user.peminjaman.index') ? 'bg-purple-600 text-white shadow-inner' : 'hover:bg-purple-600 hover:shadow-md' }}">
                📦 <span class="ml-3 font-medium">Peminjaman</span>
            </a>
        </nav>

        <!-- Logout -->
        <form action="{{ route('logout') }}" method="POST" class="mt-auto">
            @csrf
            <button class="w-full bg-red-600 hover:bg-red-700 py-2 rounded-lg text-white font-semibold transition">
                Logout
            </button>
        </form>
    </aside>

    <!-- Overlay untuk mobile -->
    <div id="overlay" 
         class="fixed inset-0 bg-black bg-opacity-40 hidden md:hidden z-30 transition-opacity duration-300 ease-in-out"
         onclick="toggleSidebar()"></div>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col md:ml-0 h-screen">

        <!-- Header (tetap di atas) -->
        <header class="flex items-center justify-between bg-white shadow px-4 py-3 sticky top-0 z-30">
            <div class="flex items-center space-x-2">
                <!-- Tombol hamburger -->
                <button id="menu-btn" class="md:hidden text-purple-700 text-2xl focus:outline-none" onclick="toggleSidebar()">
                    ☰
                </button>
                <h1 class="text-lg font-semibold text-purple-800">Dashboard Pengguna</h1>
            </div>

            <!-- Foto Profil -->
            <div class="flex items-center">
                <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3">
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

        <!-- Konten scrollable -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 animate-fadeIn bg-gray-50">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="text-center py-4 text-gray-500 text-sm border-t bg-white">
            © 2025 Sistem Peminjaman SarPras - Sarastya
        </footer>
    </div>

    <!-- Script Sidebar -->
    <script>
        let isSidebarOpen = false;

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            if (!isSidebarOpen) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('animate-slide-in');
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.add('opacity-100'), 10);
            } else {
                sidebar.classList.remove('animate-slide-in');
                sidebar.classList.add('animate-slide-out');
                overlay.classList.remove('opacity-100');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                    sidebar.classList.add('-translate-x-full');
                    sidebar.classList.remove('animate-slide-out');
                }, 300);
            }

            isSidebarOpen = !isSidebarOpen;
        }
    </script>
</body>
</html>
