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
    Schema::table('logbooks', function (Blueprint $table) {
        // Hapus kolom lama yang tidak dipakai
        $table->dropColumn(['tanggal', 'aktivitas', 'bukti_foto']); 

        // Tambah kolom baru sesuai template
        $table->integer('minggu_ke')->after('magang_id'); // Minggu ke-1, ke-2, dst
        $table->date('tgl_mulai')->after('minggu_ke'); // Tanggal Senin
        $table->date('tgl_selesai')->after('tgl_mulai'); // Tanggal Sabtu
        $table->json('isi_logbook')->after('tgl_selesai'); // Simpan Kegiatan, Masalah, Solusi per hari
    });
}

public function down()
{
    Schema::table('logbooks', function (Blueprint $table) {
        $table->date('tanggal');
        $table->text('aktivitas');
        $table->string('bukti_foto')->nullable();
        
        $table->dropColumn(['minggu_ke', 'tgl_mulai', 'tgl_selesai', 'isi_logbook']);
    });
}
};
