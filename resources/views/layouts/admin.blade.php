<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin - @yield('title', 'Dashboard Sarpras')</title>

    <!-- TailwindCSS & AlpineJS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.4s ease-in-out; }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">

    <!-- Sidebar -->
    @include('partials.admin-sidebar')

    <!-- Overlay (untuk mobile) -->
    <div id="overlay" 
         class="fixed inset-0 bg-black bg-opacity-40 hidden md:hidden z-30 transition-opacity duration-300 ease-in-out"
         onclick="toggleSidebar()"></div>

    <!-- Konten utama -->
    <div class="ml-0 md:ml-64 flex flex-col min-h-screen transition-all duration-300 ease-in-out">
        @include('partials.admin-navbar')

        <!-- Isi halaman -->
        <main class="flex-1 p-4 sm:p-6 overflow-x-auto animate-fadeIn">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="text-center py-4 text-gray-500 text-sm border-t">
            © 2025 Sistem Peminjaman SarPras - Sarastya
        </footer>
    </div>

    <!-- Script Sidebar Toggle -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const isHidden = sidebar.classList.contains('-translate-x-full');

            if (isHidden) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.add('opacity-100'), 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.remove('opacity-100');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }
    </script>

    <!-- Laravel Mix -->
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
