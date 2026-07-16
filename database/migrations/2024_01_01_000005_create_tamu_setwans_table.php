<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tamu_setwans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('instansi');
            $table->string('hari_berkunjung');
            $table->time('jam_berkunjung');
            $table->integer('jumlah_peserta');
            $table->string('nama_jabatan_ketua_rombongan');
            $table->string('nomor_hp_narahubung');
            $table->string('email');
            $table->text('alamat_instansi');
            $table->text('tujuan_kunjungan');
            $table->string('file_surat_kunjungan');
            $table->string('file_spt');
            $table->string('file_bukti_menginap')->nullable();
            $table->enum('status', ['baru', 'diproses', 'disetujui', 'ditolak', 'selesai'])->default('baru');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tamu_setwans');
    }
};
