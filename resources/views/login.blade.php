<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login - Sistem Peminjaman Sarpras</title>
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

    /* wrapper posisi icon */
    .password-wrapper {
      position: relative;
    }

    /* mata default: sedikit transparan dan hidden dulu */
    .eye-icon {
      position: absolute;
      top: 50%;
      right: 14px;
      transform: translateY(-50%);
      cursor: pointer;
      opacity: 0;
      transition: opacity 0.25s ease, transform 0.25s ease;
      width: 24px;
      height: 24px;
    }

    /* muncul fade in saat input fokus */
    .password-wrapper:focus-within .eye-icon {
      opacity: 0.8;
    }

    .eye-icon:hover {
      opacity: 1;
      transform: translateY(-50%) scale(1.1);
    }

    /* animasi input glowing */
    input {
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.2);
      color: white;
    }
    input::placeholder {
      color: rgba(255, 255, 255, 0.7);
    }
    input:focus {
      outline: none;
      box-shadow: 0 0 15px rgba(168, 85, 247, .6);
      border-color: rgba(168, 85, 247, .8);
      background: rgba(255, 255, 255, 0.25);
      transform: scale(1.01);
    }
  </style>
</head>
<body class="flex items-center justify-center px-4 py-12 gradient-bg">
  <div class="relative w-full max-w-md p-8 text-white border shadow-2xl bg-white/10 backdrop-blur-md rounded-2xl border-white/20 hover-glow">
    <h1 class="mb-2 text-2xl font-bold text-center">Login</h1>
    <p class="mb-6 text-sm text-center text-purple-200">Masukkan username dan password untuk masuk</p>

     @if ($errors->any())
    <div class="p-3 mb-4 text-sm text-red-200 border border-red-400 rounded bg-red-500/20">
        <ul class="pl-5 list-disc">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
    @endif
    
    <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
      @csrf
      <div>
        <label class="block mb-1 text-sm font-semibold">Username</label>
        <input type="text" name="username"
              class="w-full p-3 text-white placeholder-purple-200 rounded-md bg-white/20 focus:outline-none focus:ring-2 focus:ring-purple-300"
              placeholder="Masukkan Username" required>
      </div>

      <div>
        <label class="block mb-1 text-sm font-semibold">Password</label>
        <div class="password-wrapper">
          <input type="password" id="password" name="password"
                class="w-full p-3 pr-10 text-white placeholder-purple-200 rounded-md bg-white/20 focus:outline-none focus:ring-2 focus:ring-purple-300"
                placeholder="Masukkan Password" required>

          <!-- Mata tertutup (default) -->
          <svg id="togglePassword" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
              class="text-purple-200 eye-icon">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3.98 8.223C5.78 6.5 8.7 5 12 5c3.3 0 6.22 1.5 8.02 3.223.87.833 1.54 1.74 1.98 2.777-.44 1.037-1.11 1.944-1.98 2.777C18.22 17.5 15.3 19 12 19c-3.3 0-6.22-1.5-8.02-3.223A10.97 10.97 0 012 12c.44-1.037 1.11-1.944 1.98-2.777zM15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
          </svg>
        </div>
      </div>

      <button type="submit"
              class="w-full py-3 font-bold text-purple-800 transition bg-white rounded-md hover-glow hover:bg-purple-50">
        Masuk
      </button>
    </form>

    <p class="mt-6 text-sm text-center text-purple-200">
      Belum punya akun?
      <a href="{{ route('register.form') }}" class="font-semibold underline hover:text-white">Daftar di sini</a>
    </p>
  </div>

  <footer class="absolute w-full text-xs text-center text-purple-200 bottom-4">
    © 2025 Sarastya Agility Innovations. All rights reserved.
  </footer>

  <script>
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    let isVisible = false;

    togglePassword.addEventListener('click', () => {
      isVisible = !isVisible;
      passwordInput.type = isVisible ? 'text' : 'password';

      togglePassword.innerHTML = isVisible
        ? `<path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.24 5 12 5c4.76 0 8.577 2.51 9.964 6.683a1.012 1.012 0 010 .639C20.577 16.49 16.76 19 12 19c-4.76 0-8.577-2.51-9.964-6.678z" />
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`
        : `<path stroke-linecap="round" stroke-linejoin="round"
                  d="M3.98 8.223C5.78 6.5 8.7 5 12 5c3.3 0 6.22 1.5 8.02 3.223.87.833 1.54 1.74 1.98 2.777-.44 1.037-1.11 1.944-1.98 2.777C18.22 17.5 15.3 19 12 19c-3.3 0-6.22-1.5-8.02-3.223A10.97 10.97 0 012 12c.44-1.037 1.11-1.944 1.98-2.777zM15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />`;
    });
  </script>
</body>
</html>
