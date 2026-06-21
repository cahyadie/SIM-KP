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
    Schema::table('users', function (Blueprint $table) {
        $table->string('microsoft_id')->nullable()->after('email');
        $table->string('avatar')->nullable()->after('microsoft_id'); // Opsional, buat simpan foto profil
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['microsoft_id', 'avatar']);
    });
}
};
