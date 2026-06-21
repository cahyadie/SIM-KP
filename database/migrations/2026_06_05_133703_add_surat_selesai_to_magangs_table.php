<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('magangs', function (Blueprint $table) {
            $table->string('surat_selesai_magang')->nullable()->after('status_jadwal_skp');
        });
    }

    public function down()
    {
        Schema::table('magangs', function (Blueprint $table) {
            $table->dropColumn('surat_selesai_magang');
        });
    }
};
