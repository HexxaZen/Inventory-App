<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStokProsesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stok_proses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bahan_process_id')->unique(); // Relasi ke bahan_proses
            $table->decimal('stok_hasil', 10, 2)->default(0); // Stok tersisa (dalam ml, gr, atau unit)
            $table->timestamps();

            // Foreign key (opsional tergantung apakah bahan_proses ada di tabel khusus)
            $table->foreign('bahan_process_id')->references('id')->on('bahan_processes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_proses');
    }
}
