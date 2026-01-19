let trendChart, categoryChart;

function updateCharts() {
    updateTrendChart();
    updateCategoryChart();
}

function updateTrendChart() {
    const ctx = document.getElementById('trendChart');
    if (!ctx) return;

    const monthlyData = {};
    allTransaksi.forEach(t => {
        const month = new Date(t.tanggal).toISOString().slice(0, 7);
        monthlyData[month] = (monthlyData[month] || 0) + t.harga_total;
    });

    const sortedMonths = Object.keys(monthlyData).sort();

    if (trendChart) trendChart.destroy();

    trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: sortedMonths.map(m => new Date(m + '-01').toLocaleDateString('id-ID', { month: 'short', year: 'numeric' })),
            datasets: [{
                label: 'Pengeluaran (Rp)',
                data: sortedMonths.map(m => monthlyData[m]),
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { ticks: { color: 'white' } },
                y: { ticks: { color: 'white', callback: val => 'Rp ' + val.toLocaleString('id-ID') } }
            }
        }
    });
}

function updateCategoryChart() {
    const ctx = document.getElementById('categoryChart');
    if (!ctx) return;

    const categoryData = {};
    allTransaksi.forEach(t => {
        categoryData[t.kategori] = (categoryData[t.kategori] || 0) + t.harga_total;
    });

    if (categoryChart) categoryChart.destroy();

    categoryChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(categoryData),
            datasets: [{
                data: Object.values(categoryData),
                backgroundColor: [
                    'rgba(255,99,132,0.8)',
                    'rgba(54,162,235,0.8)',
                    'rgba(255,205,86,0.8)',
                    'rgba(75,192,192,0.8)',
                    'rgba(153,102,255,0.8)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            plugins: {
                legend: { labels: { color: 'white' } }
            }
        }
    });
}
