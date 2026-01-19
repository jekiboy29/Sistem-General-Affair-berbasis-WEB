@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
@endphp

<aside id="sidebar"
    class="fixed top-0 left-0 h-screen w-64 bg-gradient-to-b from-purple-700 to-purple-800 text-white flex flex-col justify-between py-6 px-4 shadow-lg z-40 transition-transform duration-300 ease-in-out transform -translate-x-full md:translate-x-0">

    <!-- Bagian Atas -->
    <div>
        <!-- Logo -->
        <div class="text-2xl font-bold text-center mb-8 select-none">
            Sarpras <span class="text-purple-200">Admin</span>
        </div>

        <!-- Navigasi -->
        <nav class="flex flex-col space-y-2">
            {{-- 📦 Data Barang --}}
            <a href="{{ route('admin.barang.dashboard') }}" 
               class="flex items-center px-4 py-2 rounded-lg transition duration-200 ease-in-out
               {{ request()->routeIs('admin.barang.dashboard') ? 'bg-purple-600 shadow-inner' : 'hover:bg-purple-600 hover:shadow-md' }}">
                📦 <span class="ml-3 font-medium">Data Barang</span>
            </a>

            {{-- 📋 Peminjaman --}}
            <a href="{{ route('admin.barang.peminjaman') }}" 
               class="flex items-center px-4 py-2 rounded-lg transition duration-200 ease-in-out
               {{ request()->routeIs('admin.barang.peminjaman') ? 'bg-purple-600 shadow-inner' : 'hover:bg-purple-600 hover:shadow-md' }}">
                📋 <span class="ml-3 font-medium">Peminjaman</span>
            </a>

            {{-- 🔁 Pengembalian --}}
            <a href="{{ route('admin.barang.pengembalian') }}" 
               class="flex items-center px-4 py-2 rounded-lg transition duration-200 ease-in-out
               {{ request()->routeIs('admin.barang.pengembalian') ? 'bg-purple-600 shadow-inner' : 'hover:bg-purple-600 hover:shadow-md' }}">
                🔁 <span class="ml-3 font-medium">Pengembalian</span>
            </a>

            {{-- 📊 Laporan --}}
            <a href="{{ route('admin.barang.laporan') }}" 
               class="flex items-center px-4 py-2 rounded-lg transition duration-200 ease-in-out
               {{ request()->routeIs('admin.barang.laporan') ? 'bg-purple-600 shadow-inner' : 'hover:bg-purple-600 hover:shadow-md' }}">
                📊 <span class="ml-3 font-medium">Laporan</span>
            </a>
        </nav>
    </div>

    <!-- Tombol Logout -->
    <form action="{{ route('logout') }}" method="POST" class="px-4">
        @csrf
        <button class="w-full bg-red-600 hover:bg-red-700 py-2 rounded-lg text-white font-semibold transition">
            Logout
        </button>
    </form>
</aside>


<script>
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');

    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
    });
</script>
