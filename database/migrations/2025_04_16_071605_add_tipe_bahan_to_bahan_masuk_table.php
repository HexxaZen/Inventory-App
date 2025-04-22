<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bahan_masuk', function (Blueprint $table) {
            $table->string('tipe')->default('non-proses')->after('nama_bahan'); // proses / non-proses
        });
    }

    public function down(): void
    {
        Schema::table('bahan_masuk', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
