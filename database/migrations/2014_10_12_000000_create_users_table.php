<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // nama lengkap
            $table->string('username')->unique(); // username untuk login
            $table->string('telegram_username')->nullable(); // username telegram
            $table->string('password');
            $table->enum('role', ['user', 'admin', 'super_admin'])->default('user'); // role user
            $table->enum('status', ['pending', 'approved'])->default('pending'); // status akun
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Balikkan migrasi.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
