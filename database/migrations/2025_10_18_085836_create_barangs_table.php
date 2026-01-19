<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->string('kategori')->nullable();
            $table->integer('jumlah')->default(0);
            $table->string('kondisi')->default('Baik'); // contoh: Baik, Rusak Ringan, Rusak Berat
            $table->string('lokasi')->nullable(); // ruangan/lantai mana
            $table->enum('status', ['tersedia', 'dipinjam', 'diperbaiki'])->default('tersedia');
            $table->integer('jumlah_rusak')->default(0);
            $table->integer('jumlah_diperbaiki')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
