<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>@yield('title', 'Dashboard') - Sarastya</title>

  {{-- ✅ TailwindCSS --}}
  <script src="https://cdn.tailwindcss.com"></script>

  {{-- ✅ Favicon --}}
  <link rel="icon" href="{{ asset('asset/favicon.png') }}">

  {{-- ✅ Custom Styles --}}
  <style>
    .gradient-bg { background: linear-gradient(to right, #4955d4, #7a91ff); }
    .hover-glow:hover { box-shadow: 0 0 15px rgba(168,85,247,.35); }
  </style>

  {{-- ✅ Slot tambahan CSS dari tiap halaman --}}
  @stack('styles')

  {{-- ✅ AlpineJS --}}
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-900 text-white font-sans min-h-screen flex flex-col">

  {{-- ✅ Navbar utama --}}
  @include('layouts.navigation')

  {{-- ✅ Konten utama halaman --}}
  <main class="flex-1 max-w-7xl mx-auto w-full px-6 py-8">
    @yield('content')
  </main>

  {{-- ✅ Footer --}}
  <footer class="text-center text-white/50 text-sm py-4 border-t border-white/10">
    © 2025 Sarastya Agility Innovations. All rights reserved.
  </footer>

  {{-- ✅ Slot tambahan JS dari tiap halaman --}}
  @stack('scripts')
</body>
</html>
