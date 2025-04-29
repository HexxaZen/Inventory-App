<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBahanProcessesTable extends Migration
{
    public function up(): void
    {
        Schema::create('bahan_processes', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bahan')->unique();
            $table->string('nama_bahan');
            $table->integer('jumlah_batch')->default (0);
            $table->integer('sisa_stok')->default(0);
            $table->integer('batas_minimum')->default(0);
            $table->string('satuan');
            $table->text('status')->nullable();

            $table->timestamps();
        });
        
        Schema::create('bahan_process_bahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bahan_process_id')->constrained('bahan_processes')->onDelete('cascade');
            $table->foreignId('bahan_id')->constrained('bahans')->onDelete('cascade');
            $table->integer('gramasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan_processes');
        Schema::dropIfExists('bahan_process_bahan');
    }
}
