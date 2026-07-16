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
        Schema::table('tamu_setwans', function (Blueprint $table) {
            $table->date('tanggal_berkunjung')->nullable()->after('jam_berkunjung');
            $table->string('file_daftar_hadir')->nullable()->after('file_bukti_menginap');
            $table->string('file_dokumen_ppid')->nullable()->after('file_daftar_hadir');
            $table->string('file_daftar_hadir_ttd')->nullable()->after('file_dokumen_ppid');
            $table->string('file_foto_kunjungan')->nullable()->after('file_daftar_hadir_ttd');
            $table->string('nomor_surat_ppid')->nullable()->after('file_foto_kunjungan');
            $table->text('materi')->nullable()->after('nomor_surat_ppid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tamu_setwans', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_berkunjung',
                'file_daftar_hadir',
                'file_dokumen_ppid',
                'file_daftar_hadir_ttd',
                'file_foto_kunjungan',
                'nomor_surat_ppid',
                'materi'
            ]);
        });
    }
};
