<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'nama_pembelian')) {
                $table->string('nama_pembelian')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'harga')) {
                $table->decimal('harga', 14, 2)->nullable();
            }
            if (!Schema::hasColumn('transactions', 'struk')) {
                $table->string('struk')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['nama_pembelian', 'qty', 'harga', 'struk']);
        });
    }
};
