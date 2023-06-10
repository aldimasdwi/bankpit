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
        Schema::create('saldo_tabungans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user');
            $table->string('saldo_tabungan')->default('0');
            $table->string('username')->nullable();
            $table->string('nama_depan');
            $table->string('nama_belakang');
            $table->string('role')->default('user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo_tabungans');
    }
};
