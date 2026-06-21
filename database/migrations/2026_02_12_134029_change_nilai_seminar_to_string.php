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
    Schema::table('magangs', function (Blueprint $table) {
        // Ubah tipe kolom menjadi string/char
        $table->string('nilai_seminar', 5)->nullable()->change();
    });
}

public function down()
{
    Schema::table('magangs', function (Blueprint $table) {
        // Kembalikan ke integer jika rollback
        $table->integer('nilai_seminar')->nullable()->change();
    });
}
};
