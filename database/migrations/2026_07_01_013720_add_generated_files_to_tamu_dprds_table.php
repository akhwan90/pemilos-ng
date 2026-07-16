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
            $table->string('file_daftar_hadir')->nullable()->after('file_bukti_menginap');
            $table->string('file_dokumen_ppid')->nullable()->after('file_daftar_hadir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tamu_dprds', function (Blueprint $table) {
            $table->dropColumn(['file_daftar_hadir', 'file_dokumen_ppid']);
        });
    }
};
