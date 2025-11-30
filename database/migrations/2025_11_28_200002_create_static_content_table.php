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
        Schema::create('static_content', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50); // terms, privacy, about
            $table->string('language', 5);
            $table->string('title');
            $table->longText('content');
            $table->string('version', 20)->default('1.0');
            $table->date('effective_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['type', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('static_content');
    }
};

