function updateAnalytics() {
    updateRiskyItems();
    updateRecommendations();
}

function updateRiskyItems() {
    const container = document.getElementById('risky-items');
    if (!container) return;

    const usageData = {};
    allBarang.forEach(b => usageData[b.nama_barang] = { ...b, usageFrequency: 0, totalUsed: 0 });

    allTransaksi.forEach(t => {
        if (usageData[t.nama_barang]) {
            usageData[t.nama_barang].usageFrequency++;
            usageData[t.nama_barang].totalUsed += t.jumlah;
        }
    });

    const risky = Object.values(usageData)
        .filter(i => i.usageFrequency > 0 && i.stok_saat_ini <= i.stok_minimum * 1.5)
        .sort((a, b) => b.usageFrequency - a.usageFrequency)
        .slice(0, 5);

    container.innerHTML = risky.length === 0
        ? `<div class="text-white/60 text-center py-4">Belum ada data risiko.</div>`
        : risky.map((i, idx) => `
            <div class="p-3 bg-white/10 rounded-xl flex justify-between">
                <div>
                    <p class="text-white font-medium">${idx+1}. ${i.nama_barang}</p>
                    <p class="text-white/60 text-sm">Dipakai ${i.usageFrequency}x</p>
                </div>
                <p class="${i.stok_saat_ini <= i.stok_minimum ? 'text-red-300' : 'text-yellow-300'} font-semibold">
                    ${i.stok_saat_ini}/${i.stok_minimum}
                </p>
            </div>
        `).join('');
}

function updateRecommendations() {
    const container = document.getElementById('recommendations');
    if (!container) return;
    // logika rekomendasi bisa disusun sama seperti sebelumnya
    container.innerHTML = `<p class="text-white/60">Fitur rekomendasi sedang dikembangkan...</p>`;
}
