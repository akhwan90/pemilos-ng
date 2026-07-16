<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aduan_aspirasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik', 16);
            $table->text('alamat');
            $table->string('pekerjaan');
            $table->string('nomor_hp');
            $table->string('email');
            $table->foreignId('kategori_aduan_id')->constrained('kategori_aduan')->cascadeOnDelete();
            $table->text('isi_aduan');
            $table->string('file_berkas_aduan')->nullable();
            $table->enum('status', ['baru', 'diproses', 'disetujui', 'ditolak', 'selesai'])->default('baru');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aduan_aspirasis');
    }
};
