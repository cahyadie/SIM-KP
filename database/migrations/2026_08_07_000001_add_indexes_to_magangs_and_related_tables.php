<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('magangs', function (Blueprint $table) {
            $table->index(['status_validasi', 'status_skp'], 'idx_magangs_validasi_skp');
            $table->index('status_gaji');
            $table->index(['tanggal_mulai', 'tanggal_selesai'], 'idx_magangs_periode');
            $table->index('created_at');
            $table->index('updated_at');
        });

        Schema::table('logbooks', function (Blueprint $table) {
            $table->index(['magang_id', 'minggu_ke'], 'idx_logbooks_magang_minggu');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
        });

        Schema::table('perusahaans', function (Blueprint $table) {
            $table->index('nama_perusahaan');
            $table->index('kategori_industri');
        });
    }

    public function down(): void
    {
        Schema::table('magangs', function (Blueprint $table) {
            $table->dropIndex('idx_magangs_validasi_skp');
            $table->dropIndex('magangs_status_gaji_index');
            $table->dropIndex('idx_magangs_periode');
            $table->dropIndex('magangs_created_at_index');
            $table->dropIndex('magangs_updated_at_index');
        });

        Schema::table('logbooks', function (Blueprint $table) {
            $table->dropIndex('idx_logbooks_magang_minggu');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
        });

        Schema::table('perusahaans', function (Blueprint $table) {
            $table->dropIndex('perusahaans_nama_perusahaan_index');
            $table->dropIndex('perusahaans_kategori_industri_index');
        });
    }
};
