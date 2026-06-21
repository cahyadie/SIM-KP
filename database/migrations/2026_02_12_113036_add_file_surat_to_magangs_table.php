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
        // Menambah kolom file setelah status_gaji
        $table->string('file_surat_kaprodi')->nullable()->after('status_gaji');
    });
}

public function down()
{
    Schema::table('magangs', function (Blueprint $table) {
        $table->dropColumn('file_surat_kaprodi');
    });
}
};
