<div class="space-y-6" data-aos="fade-up">
  <div class="grid md:grid-cols-2 gap-6">
    <!-- Barang Minim -->
    <div class="glass rounded-2xl p-6">
      <h3 class="font-semibold text-gray-700 mb-3">📉 Barang Minim Stok</h3>
      <ul id="minStockList" class="text-gray-600 space-y-1 text-sm">
        <li>Loading...</li>
      </ul>
    </div>

    <!-- Rekomendasi -->
    <div class="glass rounded-2xl p-6">
      <h3 class="font-semibold text-gray-700 mb-3">💡 Rekomendasi Otomatis</h3>
      <ul id="recommendList" class="text-gray-600 space-y-1 text-sm">
        <li>Loading...</li>
      </ul>
    </div>
  </div>

  <!-- Chart -->
  <div class="glass rounded-2xl p-6">
    <h3 class="font-semibold text-gray-700 mb-3">📊 Tren Barang Fast Moving</h3>
    <canvas id="trendChart" height="120"></canvas>
  </div>

  <!-- Riwayat -->
  <div class="glass rounded-2xl p-6">
    <h3 class="font-semibold text-gray-700 mb-3">🧾 Riwayat Transaksi Terbaru</h3>
    <table class="w-full text-sm text-gray-600">
      <thead>
        <tr class="text-left border-b border-gray-200">
          <th class="py-2">Tanggal</th>
          <th>Nama Barang</th>
          <th>Harga</th>
        </tr>
      </thead>
      <tbody id="transactionList">
        <tr><td colspan="3" class="py-2">Loading...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<script>
  async function loadDashboardData() {
    const res = await fetch('/api/dashboard');
    const data = await res.json();

    const minStock = document.getElementById('minStockList');
    const recommend = document.getElementById('recommendList');
    const txList = document.getElementById('transactionList');

    // Barang minim stok
    minStock.innerHTML = data.min_stock.length
      ? data.min_stock.map(i => `<li>${i.name} — <b>${i.stock}</b> pcs</li>`).join('')
      : '<li>Tidak ada barang minim stok</li>';

    // Rekomendasi
    recommend.innerHTML = data.recommendations.length
      ? data.recommendations.map(r => `<li>${r}</li>`).join('')
      : '<li>Tidak ada rekomendasi</li>';

    // Transaksi
    txList.innerHTML = data.transactions.length
      ? data.transactions.map(t => `<tr class="border-b border-gray-100"><td class="py-2">${t.date}</td><td>${t.item}</td><td>Rp ${t.price}</td></tr>`).join('')
      : '<tr><td colspan="3">Belum ada transaksi</td></tr>';

    // Chart
    const ctx = document.getElementById('trendChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: data.chart.labels,
        datasets: [{
          label: 'Barang Keluar',
          data: data.chart.values,
          fill: true,
          borderColor: '#ff9500',
          backgroundColor: 'rgba(255,149,0,0.1)',
          tension: 0.4
        }]
      },
      options: { responsive: true, plugins: { legend: { display: false } } }
    });
  }

  loadDashboardData();
</script>
