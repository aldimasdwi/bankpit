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
        Schema::create('data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user');
            $table->string('no_ref')->nullable();
            $table->string('transaksi')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('saldo')->nullable();
            $table->string('saldo_tabungan')->nullable();
            $table->string('username')->nullable();
            $table->string('username_penerima')->nullable();
            $table->timestamp('waktu')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data');
    }
};
