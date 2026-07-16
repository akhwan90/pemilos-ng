<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan_audiencis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nama_instansi_kelompok_paguyuban_komunitas');
            $table->text('maksud_tujuan_audiensi');
            $table->string('nama_jabatan_ketua_rombongan');
            $table->string('nomor_hp_narahubung');
            $table->integer('jumlah_peserta');
            $table->string('file_permohonan_audiensi');
            $table->enum('status', ['baru', 'diproses', 'disetujui', 'ditolak', 'selesai'])->default('baru');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan_audiencis');
    }
};
