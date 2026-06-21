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
            // Status: 'belum', 'menunggu', 'disetujui', 'ditolak'
            $table->string('status_jadwal_skp')->default('belum')->after('status_skp');

            $table->dateTime('jadwal_opsi_1')->nullable();
            $table->dateTime('jadwal_opsi_2')->nullable();
            $table->dateTime('jadwal_opsi_3')->nullable();
            $table->dateTime('jadwal_terpilih')->nullable();

            $table->text('keterangan_tolak_jadwal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magangs', function (Blueprint $table) {
            //
        });
    }
};
