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
        Schema::create('data_exports', function (Blueprint $table) {
            $table->id();
            $table->string('export_id', 20)->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('format', 10); // csv, json, pdf
            $table->string('status', 20)->default('processing'); // processing, completed, expired, failed
            $table->string('file_path')->nullable();
            $table->string('file_size')->nullable();
            $table->boolean('email_delivery')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_exports');
    }
};

