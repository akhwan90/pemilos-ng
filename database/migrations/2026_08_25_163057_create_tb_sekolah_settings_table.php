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
        Schema::create('tb_sekolah_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('npsn')->index();
            $table->integer('tahun')->index();
            $table->dateTime('selesai_at')->nullable();
            $table->longText('ppo')->nullable();
            $table->longText('hasil')->nullable();
            $table->longText('pengawas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_sekolah_settings');
    }
};
