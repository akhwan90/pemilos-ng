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
        // Rename the table first to better reflect its broader purpose
        if (Schema::hasTable('tb_kelas_generate_token')) {
            Schema::rename('tb_kelas_generate_token', 'tb_tps_setting');
        }

        // Add the new columns
        Schema::table('tb_tps_setting', function (Blueprint $table) {
            $table->dateTime('selesai_pemilihan_time')->nullable();
            
            // JSON Columns for config and results
            $table->json('perangkat_tps')->nullable();
            $table->json('form_c2_config')->nullable();
            $table->json('hasil')->nullable();
            
            // Form C1
            $table->text('form_c1_file')->nullable();
            $table->dateTime('form_c1_upload_time')->nullable();
            
            // Form C2
            $table->text('form_c2_file')->nullable();
            $table->dateTime('form_c2_upload_time')->nullable();
            
            // Form C3
            $table->text('form_c3_file')->nullable();
            $table->dateTime('form_c3_upload_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove columns
        Schema::table('tb_tps_setting', function (Blueprint $table) {
            $table->dropColumn([
                'selesai_pemilihan_time',
                'perangkat_tps',
                'form_c2_config',
                'hasil',
                'form_c1_file',
                'form_c1_upload_time',
                'form_c2_file',
                'form_c2_upload_time',
                'form_c3_file',
                'form_c3_upload_time'
            ]);
        });

        // Rename back to original
        if (Schema::hasTable('tb_tps_setting')) {
            Schema::rename('tb_tps_setting', 'tb_kelas_generate_token');
        }
    }
};