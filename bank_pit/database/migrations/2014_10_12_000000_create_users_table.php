<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama_depan');
            $table->string('nama_belakang');
            $table->enum('jenis_kelamin',['laki-laki','perempuan']);
            $table->string('no_hp');
            $table->string('tempat');
            $table->string('tanggal');
            $table->string('bulan');
            $table->string('tahun');
            $table->string('provinsi');
            $table->string('kota');
            $table->string('alamat_lengkap');
            $table->string('role')->default('user');
            $table->string('username')->unique();
            $table->string('pin');
            $table->string('is_activated')->default('off');
            $table->string('no_unique');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
