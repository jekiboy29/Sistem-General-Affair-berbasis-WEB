window.allBarang = [];
window.allTransaksi = [];

async function fetchAllData() {
    try {
        const [barangRes, transaksiRes] = await Promise.all([
            fetch('/api/barang'),
            fetch('/api/transaksi')
        ]);

        allBarang = await barangRes.json();
        allTransaksi = await transaksiRes.json();

        updateDashboard();
    } catch (err) {
        console.error('Fetch error:', err);
    }
}

function updateDashboard() {
    updateStats();
    updateCharts();
    updateAnalytics();
}

function updateStats() {
    const available = allBarang.filter(b => b.status === 'tersedia').length;
    const borrowed = allBarang.filter(b => b.status === 'dipinjam').length;
    const broken = allBarang.filter(b => b.status === 'rusak').length;

    document.getElementById('stat-available').textContent = available;
    document.getElementById('stat-borrowed').textContent = borrowed;
    document.getElementById('stat-broken').textContent = broken;
}

document.addEventListener('DOMContentLoaded', fetchAllData);
