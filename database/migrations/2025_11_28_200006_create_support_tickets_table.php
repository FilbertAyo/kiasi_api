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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_id', 20)->unique();
            $table->string('name', 100);
            $table->string('email');
            $table->string('subject', 100);
            $table->text('message');
            $table->string('category', 30)->nullable(); // general, account, billing, technical
            $table->string('status', 20)->default('open'); // open, in_progress, resolved, closed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};

