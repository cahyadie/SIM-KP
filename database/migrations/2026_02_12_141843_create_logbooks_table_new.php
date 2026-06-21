<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Kita drop dulu tabelnya jika ada biar bersih
        Schema::dropIfExists('logbooks');

        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magang_id')->constrained('magangs')->onDelete('cascade');
            
            // Kolom Baru untuk Logbook Mingguan
            $table->integer('minggu_ke'); // Minggu ke-1, 2, dst
            $table->date('tgl_mulai');    // Senin
            $table->date('tgl_selesai');  // Sabtu
            $table->json('isi_logbook');  // Data Kegiatan, Masalah, Solusi (JSON)
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('logbooks');
    }
};