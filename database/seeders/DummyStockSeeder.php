<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\Transaction;

class DummyStockSeeder extends Seeder
{
    public function run()
    {
        // 🔒 Matikan dulu foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Hapus data lama
        Transaction::truncate();
        Item::truncate();

        // 🔓 Nyalakan lagi foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Buat beberapa item
        $items = [
            'Kursi',
            'Meja',
            'Proyektor',
            'Laptop',
        ];

        foreach ($items as $name) {
            $item = Item::create([
                'name' => $name,
                'current_stock' => 0,
            ]);

            // 3 transaksi masuk
            for ($i = 0; $i < 3; $i++) {
                Transaction::create([
                    'item_id' => $item->id,
                    'harga' => rand(100000, 500000),
                    'type' => 'in',
                    'qty' => rand(5, 15),
                    'price_per_unit' => rand(100000, 500000),
                    'note' => 'Restock otomatis',
                ]);
            }

            // 2 transaksi keluar
            for ($i = 0; $i < 2; $i++) {
                Transaction::create([
                    'item_id' => $item->id,
                    'harga' => rand(100000, 500000),
                    'type' => 'out',
                    'qty' => rand(2, 8),
                    'price_per_unit' => rand(100000, 500000),
                    'note' => 'Penggunaan barang',
                ]);
            }
        }
    }
}
