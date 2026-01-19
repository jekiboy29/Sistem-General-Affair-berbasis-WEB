<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusJumlahToBarangsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('barangs', function (Blueprint $table) {
            // Kolom sudah dibuat di migration create_barangs_table, jadi ini dikosongkan untuk menghindari error duplikat.
            // if (!Schema::hasColumn('barangs', 'jumlah_rusak')) { ... }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

    public function down()
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn(['jumlah_rusak', 'jumlah_diperbaiki']);
        });
    }
}
