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
        Schema::table('tamu_dprds', function (Blueprint $table) {
            $table->string('file_daftar_hadir_ttd')->nullable()->after('file_dokumen_ppid');
            $table->string('file_foto_kunjungan')->nullable()->after('file_daftar_hadir_ttd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tamu_dprds', function (Blueprint $table) {
            $table->dropColumn(['file_daftar_hadir_ttd', 'file_foto_kunjungan']);
        });
    }
};
