<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('menu', function (Blueprint $table) {
            $table->string('kategori_id')->default('Tidak Tersedia')->after('status_menu');
        });
    }

    public function down()
    {
        Schema::table('menu', function (Blueprint $table) {
            $table->dropColumn('kategori_id');
        });
    }
};
