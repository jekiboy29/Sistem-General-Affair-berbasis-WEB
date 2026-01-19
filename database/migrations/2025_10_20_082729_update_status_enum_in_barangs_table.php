<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah kolom status agar mendukung opsi baru
        DB::statement("ALTER TABLE barangs MODIFY COLUMN status ENUM('tersedia', 'dipinjam', 'diperbaiki', 'rusak', 'tidak bisa dipinjam') DEFAULT 'tersedia'");
    }

    public function down(): void
    {
        // Rollback ke enum lama
        DB::statement("ALTER TABLE barangs MODIFY COLUMN status ENUM('tersedia', 'dipinjam', 'diperbaiki') DEFAULT 'tersedia'");
    }
};
