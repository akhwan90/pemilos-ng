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
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // e.g., 'malicious_file_upload', 'rate_limit_exceeded', 'unauthorized_access'
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('endpoint')->nullable(); // Route/URL
            $table->json('payload')->nullable(); // Data yang dicurigai (Request Body / File Meta)
            $table->unsignedBigInteger('user_id')->nullable(); // Jika admin yang login
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
