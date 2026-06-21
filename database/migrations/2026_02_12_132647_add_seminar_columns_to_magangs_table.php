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
    Schema::table('magangs', function (Blueprint $table) {
        $table->string('file_seminar')->nullable()->after('file_surat_kaprodi');
        $table->integer('nilai_seminar')->nullable()->after('file_seminar');
    });
}

public function down()
{
    Schema::table('magangs', function (Blueprint $table) {
        $table->dropColumn(['file_seminar', 'nilai_seminar']);
    });
}
};
