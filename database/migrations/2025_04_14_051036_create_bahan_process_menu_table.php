<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('bahan_process_menu', function (Blueprint $table) {
        $table->id();
        $table->foreignId('menu_id')->constrained('menu')->onDelete('cascade');
        $table->foreignId('bahan_id')->constrained('bahan_processes')->onDelete('cascade');
        $table->decimal('gramasi', 8, 2); // Kolom gramasi untuk menyimpan jumlah gramasi bahan
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_process_menu');
    }
};
