<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('magangs', function (Blueprint $table) {
            // 1. Cek Kolom Nilai Seminar
            if (!Schema::hasColumn('magangs', 'nilai_seminar')) {
                $table->string('nilai_seminar', 5)->nullable()->after('status_gaji');
            }

            // 2. Cek Kolom File Seminar
            if (!Schema::hasColumn('magangs', 'file_seminar')) {
                $table->string('file_seminar')->nullable()->after('nilai_seminar');
            }

            // 3. Cek Kolom Status SKP
            if (!Schema::hasColumn('magangs', 'status_skp')) {
                $table->enum('status_skp', ['belum', 'sudah'])->default('belum')->after('file_seminar');
            }

            // 4. Cek Kolom Keterangan Revisi (INI YANG PALING PENTING)
            if (!Schema::hasColumn('magangs', 'keterangan_revisi')) {
                $table->text('keterangan_revisi')->nullable()->after('status_skp');
            }
        });
    }

    public function down()
    {
        Schema::table('magangs', function (Blueprint $table) {
            // Kosongkan saja agar aman saat rollback
        });
    }
};