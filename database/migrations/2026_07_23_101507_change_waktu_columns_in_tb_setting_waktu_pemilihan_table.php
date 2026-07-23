<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change sql_mode temporarily for this migration to avoid strict mode datetime issues
        DB::statement("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
        
        // Convert zero dates to NULL before altering type
        DB::statement("UPDATE tb_setting_waktu_pemilihan SET waktu_mulai = NULL WHERE CAST(waktu_mulai AS CHAR) LIKE '0000-00-00%'");
        DB::statement("UPDATE tb_setting_waktu_pemilihan SET waktu_selesai = NULL WHERE CAST(waktu_selesai AS CHAR) LIKE '0000-00-00%'");

        // Now safe to modify the columns
        DB::statement("ALTER TABLE tb_setting_waktu_pemilihan MODIFY waktu_mulai DATETIME NULL");
        DB::statement("ALTER TABLE tb_setting_waktu_pemilihan MODIFY waktu_selesai DATETIME NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
        DB::statement("ALTER TABLE tb_setting_waktu_pemilihan MODIFY waktu_mulai DATE NULL");
        DB::statement("ALTER TABLE tb_setting_waktu_pemilihan MODIFY waktu_selesai DATE NULL");
    }
};
