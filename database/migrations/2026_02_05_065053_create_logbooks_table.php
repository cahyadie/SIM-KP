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
    Schema::create('logbooks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('magang_id')->constrained('magangs')->onDelete('cascade');
        $table->date('tanggal');
        $table->text('aktivitas');
        $table->string('bukti_foto')->nullable(); // Path file upload
        $table->enum('status', ['pending', 'approved'])->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};
