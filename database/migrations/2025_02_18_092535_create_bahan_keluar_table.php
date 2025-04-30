<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bahan_keluar', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_keluar');
            $table->string('kode_bahan', 50);
            $table->string('nama_bahan', 100);
            $table->integer('jumlah_keluar');
            $table->string('satuan', 20);

            // Tambahkan kolom foreign key nullable
            $table->unsignedBigInteger('bahan_masuk_id')->nullable();
            $table->foreign('bahan_masuk_id')->references('id')->on('bahan_masuk')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahan_keluar', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu sebelum drop tabel
            $table->dropForeign(['bahan_masuk_id']);
        });

        Schema::dropIfExists('bahan_keluar');
    }
};
