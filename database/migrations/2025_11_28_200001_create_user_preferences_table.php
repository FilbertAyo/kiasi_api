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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            
            // General preferences
            $table->string('language', 5)->default('en');
            $table->string('currency', 10)->default('TZS');
            $table->string('date_format', 20)->default('DD/MM/YYYY');
            $table->string('number_format', 20)->default('1,000.00');
            $table->string('first_day_of_week', 10)->default('monday');
            
            // Notification preferences
            $table->boolean('push_enabled')->default(true);
            $table->boolean('daily_reminder')->default(true);
            $table->time('daily_reminder_time')->default('20:00:00');
            $table->boolean('budget_alerts')->default(true);
            $table->boolean('weekly_summary')->default(true);
            
            // Privacy/Security preferences
            $table->boolean('app_lock_enabled')->default(false);
            $table->string('app_lock_pin')->nullable();
            $table->boolean('biometric_enabled')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};

