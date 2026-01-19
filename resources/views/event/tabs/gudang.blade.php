<div class="space-y-6" data-aos="fade-up">
  <div class="flex justify-between items-center">
    <h3 class="font-semibold text-gray-700 text-lg">📦 Daftar Barang Gudang</h3>
    <button id="btnAddItem" class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-all">+ Tambah Barang</button>
  </div>

  <!-- Tabel Barang -->
  <div class="glass rounded-2xl p-4 overflow-x-auto">
    <table class="w-full text-sm text-gray-600" id="itemsTable">
      <thead>
        <tr class="text-left border-b border-gray-200">
          <th class="py-2">Nama</th>
          <th>Kategori</th>
          <th>Stok</th>
          <th>Satuan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody id="itemsBody">
        <tr><td colspan="5" class="py-3 text-center">Loading...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Tambah/Edit -->
<div id="itemModal" class="fixed inset-0 bg-black/40 hidden justify-center items-center z-50">
  <div class="bg-white rounded-2xl p-6 w-96">
    <h3 id="modalTitle" class="font-semibold mb-4">Tambah Barang</h3>
    <form id="itemForm" class="space-y-3">
      <input type="hidden" id="itemId">
      <input type="text" id="itemName" placeholder="Nama Barang" class="w-full border p-2 rounded-lg" required>
      <input type="text" id="itemCategory" placeholder="Kategori (opsional)" class="w-full border p-2 rounded-lg">
      <input type="number" id="itemStock" placeholder="Stok" class="w-full border p-2 rounded-lg" required>
      <input type="text" id="itemUnit" placeholder="Satuan (pcs, box, dll)" class="w-full border p-2 rounded-lg">
      <div class="flex justify-end space-x-2">
        <button type="button" id="btnCancel" class="px-3 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Batal</button>
        <button type="submit" class="px-3 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600">Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
  const modal = document.getElementById('itemModal');
  const form = document.getElementById('itemForm');
  const title = document.getElementById('modalTitle');
  const btnAdd = document.getElementById('btnAddItem');
  const btnCancel = document.getElementById('btnCancel');

  async function loadItems() {
    const res = await fetch('/api/items');
    const data = await res.json();
    const body = document.getElementById('itemsBody');

    if (!data.length) {
      body.innerHTML = `<tr><td colspan="5" class="py-3 text-center">Belum ada data barang</td></tr>`;
      return;
    }

    body.innerHTML = data.map(i => `
      <tr class="border-b border-gray-100">
        <td class="py-2">${i.name}</td>
        <td>${i.category || '-'}</td>
        <td>${i.stock}</td>
        <td>${i.unit || '-'}</td>
        <td>
          <button onclick="editItem(${i.id})" class="text-blue-500 hover:text-blue-700">Edit</button>
          <button onclick="deleteItem(${i.id})" class="text-red-500 hover:text-red-700 ml-2">Hapus</button>
        </td>
      </tr>
    `).join('');
  }

  btnAdd.onclick = () => {
    title.textContent = 'Tambah Barang';
    form.reset();
    document.getElementById('itemId').value = '';
    modal.classList.remove('hidden');
  };

  btnCancel.onclick = () => modal.classList.add('hidden');

  form.onsubmit = async e => {
    e.preventDefault();
    const id = document.getElementById('itemId').value;
    const payload = {
      name: itemName.value,
      category: itemCategory.value,
      stock: itemStock.value,
      unit: itemUnit.value
    };

    await fetch(id ? `/api/items/${id}` : '/api/items', {
      method: id ? 'PUT' : 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    modal.classList.add('hidden');
    loadItems();
  };

  async function editItem(id) {
    const res = await fetch('/api/items');
    const data = await res.json();
    const item = data.find(i => i.id === id);
    if (!item) return;

    document.getElementById('itemId').value = id;
    document.getElementById('itemName').value = item.name;
    document.getElementById('itemCategory').value = item.category || '';
    document.getElementById('itemStock').value = item.stock;
    document.getElementById('itemUnit').value = item.unit || '';
    title.textContent = 'Edit Barang';
    modal.classList.remove('hidden');
  }

  async function deleteItem(id) {
    if (!confirm('Yakin hapus barang ini?')) return;
    await fetch(`/api/items/${id}`, { method: 'DELETE' });
    loadItems();
  }

  loadItems();
</script>
