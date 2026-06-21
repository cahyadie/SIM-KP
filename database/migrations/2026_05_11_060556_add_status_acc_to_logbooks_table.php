<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('logbooks', function (Blueprint $table) {
            // Kolom penanda ACC, default false (belum di-ACC)
            $table->boolean('status_acc')->default(false)->after('isi_logbook');
        });
    }

    public function down()
    {
        Schema::table('logbooks', function (Blueprint $table) {
            $table->dropColumn('status_acc');
        });
    }
};