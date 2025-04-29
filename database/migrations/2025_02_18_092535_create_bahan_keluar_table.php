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

            // Tambahkan kolom bahan_masuk_id sebagai foreign key
            $table->unsignedBigInteger('bahan_masuk_id'); // Menambahkan kolom foreign key
            $table->foreign('bahan_masuk_id')->references('id')->on('bahan_masuk')->onDelete('cascade'); // Menambahkan relasi ke tabel bahan_masuk

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahan_keluar', function (Blueprint $table) {
            $table->dropForeign(['bahan_masuk_id']); // Hapus relasi
        });
        Schema::dropIfExists('bahan_keluar');
    }
};
