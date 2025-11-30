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
        Schema::create('faq_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('name_en');
            $table->string('name_sw');
            $table->string('icon', 50)->default('help');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('faq_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('faq_categories')->onDelete('cascade');
            $table->string('language', 5);
            $table->text('question');
            $table->text('answer');
            $table->unsignedInteger('helpful_count')->default(0);
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Track which users found which FAQs helpful
        Schema::create('faq_helpful_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('faq_question_id')->constrained('faq_questions')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_id', 'faq_question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq_helpful_votes');
        Schema::dropIfExists('faq_questions');
        Schema::dropIfExists('faq_categories');
    }
};

