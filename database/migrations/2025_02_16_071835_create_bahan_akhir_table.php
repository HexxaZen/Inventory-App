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
        Schema::create('bahan_akhir', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_input');
            $table->string('kode_bahan', 50);
            $table->string('kategori_bahan',50);
            $table->string('nama_bahan', 100);
            $table->integer('stok_terakhir');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_akhir');
    }
};
