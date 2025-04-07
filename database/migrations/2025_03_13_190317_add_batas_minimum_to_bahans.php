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
    Schema::table('bahans', function (Blueprint $table) {
        $table->integer('batas_minimum')->default(5)->after('sisa_stok'); // Misal defaultnya 5
    });
}

public function down()
{
    Schema::table('bahans', function (Blueprint $table) {
        $table->dropColumn('batas_minimum');
    });
}

};
