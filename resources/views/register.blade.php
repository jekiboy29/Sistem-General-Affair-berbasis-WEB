<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Daftar Akun - Sistem Peminjaman Sarpras</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="{{ asset('asset/favicon.png') }}">
  <style>
    .gradient-bg {
      background: linear-gradient(to bottom, #cbb2ff, #8e5fe8, #4a148c);
      min-height: 100vh;
    }
    .hover-glow {
      transition: all .3s ease;
    }
    .hover-glow:hover {
      box-shadow: 0 0 30px rgba(168, 85, 247, .45);
      transform: translateY(-4px);
    }
    input, select {
      transition: all .3s ease;
      background: rgba(255,255,255,0.2);
      color: white;
    }
    input::placeholder, select {
      color: rgba(255,255,255,0.7);
    }
    input:focus, select:focus {
      outline: none;
      box-shadow: 0 0 15px rgba(168, 85, 247, .6);
      border-color: rgba(168, 85, 247, .8);
    }

    /* posisi ikon mata sejajar pas tengah kanan input */
    .eye-icon {
      position: absolute;
      top: 65%;
      right: 14px;
      transform: translateY(-50%);
      cursor: pointer;
      opacity: 0;
      width: 22px;
      height: 22px;
      transition: opacity 0.25s ease, transform 0.25s ease;
      pointer-events: auto;
    }

    /* muncul halus pas input fokus */
    .input-group:focus-within .eye-icon {
      opacity: 0.8;
    }

    .eye-icon:hover {
      opacity: 1;
      transform: translateY(-50%) scale(1.1);
    }
  </style>
</head>
<body class="gradient-bg flex items-center justify-center py-12 px-4">
  <div class="bg-white/10 backdrop-blur-md p-8 rounded-2xl w-full max-w-md text-white shadow-2xl border border-white/20 hover-glow">
    <h1 class="text-2xl font-bold mb-2 text-center">Daftar Akun Baru</h1>
    <p class="text-purple-200 text-sm text-center mb-6">Isi data di bawah untuk mendaftar ke sistem Sarastya</p>

    <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
      @csrf

      <div>
        <label class="block mb-1 text-sm font-semibold">Nama Lengkap</label>
        <input type="text" name="name" class="w-full p-3 rounded-md bg-white/25 text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-300" placeholder="Masukkan Nama Lengkap" required>
      </div>

      <div>
        <label class="block mb-1 text-sm font-semibold">Username</label>
        <input type="text" name="username" class="w-full p-3 rounded-md bg-white/25 text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-300" placeholder="Buat Username" required>
      </div>

      <div>
        <label class="block mb-1 text-sm font-semibold">Username Telegram</label>
        <input type="text" name="telegram_username" class="w-full p-3 rounded-md bg-white/25 text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-300" placeholder="Masukkan Username Telegram" required>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <!-- Password -->
        <div class="relative input-group">
          <label class="block mb-1 text-sm font-semibold">Password</label>
          <input type="password" id="password" name="password"
                 class="w-full p-3 rounded-md bg-white/25 text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-300 pr-10"
                 placeholder="Buat Password" required>
          <!-- Icon mata -->
          <svg id="togglePassword" xmlns="http://www.w3.org/2000/svg" fill="none"
               viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
               class="eye-icon text-purple-200">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 3l18 18M9.88 9.88A3 3 0 0114.12 14.12M9.88 9.88L5.7 5.7m12.6 12.6L14.12 14.12m-4.24-4.24L3 3m18 18c-1.72-1.72-4.06-3-7-3s-5.28 1.28-7 3" />
          </svg>
        </div>

        <!-- Konfirmasi Password -->
        <div class="relative input-group">
          <label class="block mb-1 text-sm font-semibold">Konfirmasi Password</label>
          <input type="password" id="password_confirmation" name="password_confirmation"
                 class="w-full p-3 rounded-md bg-white/25 text-white placeholder-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-300 pr-10"
                 placeholder="Ulangi Password" required>
          <!-- Icon mata -->
          <svg id="togglePasswordConfirm" xmlns="http://www.w3.org/2000/svg" fill="none"
               viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
               class="eye-icon text-purple-200">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 3l18 18M9.88 9.88A3 3 0 0114.12 14.12M9.88 9.88L5.7 5.7m12.6 12.6L14.12 14.12m-4.24-4.24L3 3m18 18c-1.72-1.72-4.06-3-7-3s-5.28 1.28-7 3" />
          </svg>
        </div>
      </div>

      <div>
        <label class="block mb-1 text-sm font-semibold">Role</label>
        <select name="role" class="w-full p-3 rounded-md bg-white/25 text-white focus:outline-none focus:ring-2 focus:ring-purple-300 appearance-none cursor-pointer transition duration-200" required>
          <option value="" class="bg-purple-700 text-white">-- Pilih Role --</option>
          <option value="user" class="bg-purple-700 text-white">User</option>
          <option value="admin" class="bg-purple-700 text-white">Admin</option>
        </select>
      </div>

      <button type="submit" class="w-full bg-white text-purple-800 font-bold py-3 rounded-md hover-glow hover:bg-purple-50 transition">
        Daftar
      </button>
    </form>

    <p class="text-center text-sm text-purple-200 mt-6">
      Sudah punya akun?
      <a href="{{ route('login') }}" class="font-semibold underline hover:text-white">Login di sini</a>
    </p>
  </div>

  <footer class="absolute bottom-1 w-full text-center text-purple-200 text-xs">
    © 2025 Sarastya Agility Innovations. All rights reserved.
  </footer>

  <script>
    // toggle mata (awal: mata dicoret)
    const setupPasswordToggle = (inputId, toggleId) => {
      const input = document.getElementById(inputId);
      const toggle = document.getElementById(toggleId);
      let visible = false;

      toggle.addEventListener("click", () => {
        visible = !visible;
        input.type = visible ? "text" : "password";
        toggle.innerHTML = visible
          ? `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.24 5 12 5c4.76 0 8.577 2.51 9.964 6.683a1.012 1.012 0 010 .639C20.577 16.49 16.76 19 12 19c-4.76 0-8.577-2.51-9.964-6.678z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`
          : `<path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M9.88 9.88A3 3 0 0114.12 14.12M9.88 9.88L5.7 5.7m12.6 12.6L14.12 14.12m-4.24-4.24L3 3m18 18c-1.72-1.72-4.06-3-7-3s-5.28 1.28-7 3" />`;
      });
    };

    setupPasswordToggle("password", "togglePassword");
    setupPasswordToggle("password_confirmation", "togglePasswordConfirm");
  </script>
</body>
</html>
