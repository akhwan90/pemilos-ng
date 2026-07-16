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
        Schema::table('permohonan_audiencis', function (Blueprint $table) {
            $table->string('file_daftar_hadir')->nullable()->after('file_permohonan_audiensi');
            $table->string('file_dokumen_ppid')->nullable()->after('file_daftar_hadir');
            $table->string('nomor_surat_ppid')->nullable()->after('file_dokumen_ppid');
            // Menambahkan tanggal pelaksanaan untuk dokumen
            $table->date('tanggal_pelaksanaan')->nullable()->after('nomor_surat_ppid');
            $table->time('jam_pelaksanaan')->nullable()->after('tanggal_pelaksanaan');
            // Alkap / Komisi yang menemui
            $table->string('alkap_penerima')->nullable()->after('jam_pelaksanaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_audiencis', function (Blueprint $table) {
            $table->dropColumn([
                'file_daftar_hadir',
                'file_dokumen_ppid',
                'nomor_surat_ppid',
                'tanggal_pelaksanaan',
                'jam_pelaksanaan',
                'alkap_penerima'
            ]);
        });
    }
};
