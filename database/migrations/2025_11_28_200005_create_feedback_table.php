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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ticket_id', 20)->unique();
            $table->string('type', 20); // feedback, bug_report, feature_request
            $table->string('category', 30)->nullable(); // general, feature_request, bug, performance, ui_ux
            $table->string('subject', 100);
            $table->text('message');
            $table->unsignedTinyInteger('rating')->nullable();
            $table->json('attachments')->nullable();
            $table->string('status', 20)->default('received'); // received, in_progress, resolved, closed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};

