<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('magangs', function (Blueprint $table) {
            $table->dateTime('jadwal_opsi_4')->nullable();
            $table->dateTime('jadwal_opsi_5')->nullable();
            $table->dateTime('jadwal_opsi_6')->nullable();
            $table->dateTime('jadwal_opsi_7')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('magangs', function (Blueprint $table) {
            $table->dropColumn(['jadwal_opsi_4', 'jadwal_opsi_5', 'jadwal_opsi_6', 'jadwal_opsi_7']);
        });
    }
};
