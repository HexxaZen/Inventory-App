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
        Schema::create('komposisis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bahan_process_id');
            $table->unsignedBigInteger('bahan_id'); // merujuk ke bahan_process lain
            $table->double('gramasi');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('bahan_process_id')->references('id')->on('bahan_processes')->onDelete('cascade');
            $table->foreign('bahan_id')->references('id')->on('bahan_processes')->onDelete('cascade');

            // Unique constraint untuk mencegah duplikat
            $table->unique(['bahan_process_id', 'bahan_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('komposisis');
    }
};
