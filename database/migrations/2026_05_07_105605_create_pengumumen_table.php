<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->string('judul'); 
            $table->text('deskripsi'); // Teks pembuka
            $table->string('lokasi')->nullable();
            $table->string('info_gaji')->nullable(); // cth: Tidak ada gaji
            $table->string('info_fasilitas')->nullable(); // cth: Tidak ada fasilitas tempat tinggal
            $table->string('syarat_tambahan')->nullable(); // cth: Diutamakan domisili Jakarta
            $table->string('target_angkatan')->nullable(); // cth: Khusus mahasiswa 2022
            $table->string('link_pendaftaran');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengumuman');
    }
};