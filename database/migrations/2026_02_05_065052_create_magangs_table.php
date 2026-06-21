<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('magangs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
        $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
        // Dosen bisa kosong dulu saat pendaftaran awal
        $table->foreignId('dosen_id')->nullable()->constrained('users')->onDelete('set null'); 
        
        $table->date('tanggal_mulai');
        $table->date('tanggal_selesai');
        
        // Status Penting untuk Dashboard Kaprodi
        $table->enum('status_gaji', ['paid', 'unpaid'])->default('unpaid');
        $table->enum('status_skp', ['belum', 'sudah'])->default('belum');
        $table->enum('status_validasi', ['pending', 'diterima', 'ditolak'])->default('pending');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magangs');
    }
};
