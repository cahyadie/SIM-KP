<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // KITA KOSONGKAN INI AGAR LARAVEL TIDAK MENAMBAH KOLOM DOSEN_ID LAGI
        // KARENA KOLOMNYA SUDAH ADA DI DATABASE
    }

    public function down()
    {
        // Biarkan kosong
    }
};