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
        Schema::table('menu_terjual', function (Blueprint $table) {
                $table->date('tanggal')->after('jumlah_terjual');
            });
            
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_terjual', function (Blueprint $table) {
            //
        });
    }
};
