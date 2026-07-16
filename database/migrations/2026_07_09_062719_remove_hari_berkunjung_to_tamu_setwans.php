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
            $table->dropColumn('hari_berkunjung');
        });

        Schema::table('tamu_dprds', function (Blueprint $table) {
            $table->dropColumn('hari_berkunjung');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tamu_setwans', function (Blueprint $table) {
            $table->string('hari_berkunjung');
        });

        Schema::table('tamu_dprds', function (Blueprint $table) {
            $table->string('hari_berkunjung');
        });
    }
};
