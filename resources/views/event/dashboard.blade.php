<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Stok Opname | Sarastya</title>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- AOS Animation -->
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <!-- GSAP for smooth transitions -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body {
      background: linear-gradient(135deg, #f7f7f9, #ffffff);
      backdrop-filter: blur(20px);
      font-family: 'Inter', sans-serif;
    }
    .glass {
      background: rgba(255, 255, 255, 0.45);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.25);
      box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
    }
    .active-tab {
      background-color: rgba(255, 149, 0, 0.15);
      color: #ff9500;
      font-weight: 600;
    }
  </style>
</head>
<body class="h-screen flex">

  <!-- Sidebar -->
  <aside class="w-64 p-6 glass flex flex-col justify-between">
    <div>
      <h1 class="text-2xl font-bold mb-6 text-gray-800">Stok Opname</h1>
      <nav class="space-y-2">
        <button class="tab-btn w-full text-left px-4 py-2 rounded-lg hover:bg-orange-50" data-tab="dashboard">Dashboard</button>
        <button class="tab-btn w-full text-left px-4 py-2 rounded-lg hover:bg-orange-50" data-tab="gudang">Gudang</button>
        <button class="tab-btn w-full text-left px-4 py-2 rounded-lg hover:bg-orange-50" data-tab="transaksi">Transaksi</button>
        <button class="tab-btn w-full text-left px-4 py-2 rounded-lg hover:bg-orange-50" data-tab="laporan">Laporan</button>
      </nav>
    </div>

    <form method="POST" action="{{ route('event.logout') }}">
      @csrf
      <button class="w-full mt-8 py-2 bg-red-100 text-red-600 font-semibold rounded-lg hover:bg-red-200 transition">
        Logout
      </button>
    </form>
  </aside>

    <!-- Main Content -->
  <main id="main-content" class="flex-1 overflow-y-auto p-8 space-y-8">
    <div id="tab-content" data-aos="fade-up">
      <p class="text-gray-400">Memuat tampilan...</p>
    </div>
  </main>


<script>
AOS.init({ duration: 600, once: true });

const tabs = document.querySelectorAll('.tab-btn');
const contentContainer = document.getElementById('tab-content');

async function loadTab(tabName) {
  contentContainer.innerHTML = `<p class="text-gray-400 animate-pulse">⏳ Memuat ${tabName}...</p>`;
  try {
    const res = await fetch(`/event/tabs/${tabName}`);
    const html = await res.text();

    // Masukkan HTML
    contentContainer.innerHTML = html;

    // Cari dan jalankan semua <script> di dalam hasil fetch
    const scripts = contentContainer.querySelectorAll("script");
    scripts.forEach(oldScript => {
      const newScript = document.createElement("script");
      if (oldScript.src) {
        newScript.src = oldScript.src;
      } else {
        newScript.textContent = oldScript.textContent;
      }
      document.body.appendChild(newScript);
      oldScript.remove();
    });

    AOS.refresh();
    gsap.fromTo("#tab-content", { opacity: 0, y: 10 }, { opacity: 1, y: 0, duration: 0.4 });
  } catch (err) {
    contentContainer.innerHTML = `<p class="text-red-500">Gagal memuat ${tabName}</p>`;
  }
}

tabs.forEach(btn => {
  btn.addEventListener('click', () => {
    tabs.forEach(b => b.classList.remove('active-tab'));
    btn.classList.add('active-tab');
    loadTab(btn.dataset.tab);
  });
});

loadTab('dashboard');
</script>


</body>
</html>
