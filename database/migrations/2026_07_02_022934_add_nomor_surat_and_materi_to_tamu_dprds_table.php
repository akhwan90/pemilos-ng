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
            $table->string('nomor_surat_ppid')->nullable()->after('file_foto_kunjungan');
            $table->text('materi')->nullable()->after('nomor_surat_ppid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tamu_dprds', function (Blueprint $table) {
            $table->dropColumn(['nomor_surat_ppid', 'materi']);
        });
    }
};
