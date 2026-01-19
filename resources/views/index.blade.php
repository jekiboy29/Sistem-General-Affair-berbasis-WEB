<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Sistem Peminjaman Sarpras - Sarastya</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="{{ asset('asset/favicon.png') }}">
  <style>
    .gradient-bg {
      background: linear-gradient(to bottom, #cbb2ff, #8e5fe8, #4a148c);
      min-height: 100vh;
    }
    .hover-glow {
      transition: all .3s;
    }
    .hover-glow:hover {
      box-shadow: 0 0 30px rgba(168, 85, 247, .35);
      transform: translateY(-2px);
    }
  </style>
</head>
<body class="gradient-bg font-sans">
  <div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="max-w-2xl w-full text-center">
      <img src="{{ asset('asset/Logo Sarastya_Logo_Only.png') }}" alt="Logo" class="mx-auto mb-8 w-40">
      <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight">
        Hai, aku adalah
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-200 to-pink-200">Sistem Peminjaman Sarpras</span>
        <br><span class="text-purple-200">milik Sarastya</span>
      </h1>
      <p class="text-xl md:text-2xl text-purple-100 mb-12 leading-relaxed max-w-xl mx-auto">
        Silahkan login untuk melakukan peminjaman sarana prasarana ya!
      </p>

      <div class="flex gap-4 justify-center">
        <a href="{{ route('login') }}" class="bg-white text-purple-800 font-bold text-lg px-8 py-3 rounded-full hover:bg-purple-50 hover-glow">Login</a>
        <a href="{{ route('register.form') }}" class="border border-white/40 text-white font-semibold px-6 py-3 rounded-full hover:bg-white/10 hover:scale-105 transition">Daftar</a>
      </div>

      <div class="mt-16 flex justify-center space-x-8 opacity-30">
        <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
        <div class="w-3 h-3 bg-purple-200 rounded-full animate-pulse" style="animation-delay:.5s"></div>
        <div class="w-3 h-3 bg-pink-200 rounded-full animate-pulse" style="animation-delay:1s"></div>
      </div>
    </div>
  </div>
  <footer class="text-purple-200 text-sm text-center pb-6">© 2025 Sarastya Agility Innovations. All rights reserved.</footer>
</body>
</html>
