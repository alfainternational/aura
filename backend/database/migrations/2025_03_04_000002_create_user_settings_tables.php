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
        // User interface settings table
        Schema::create('user_ui_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('theme')->default('light'); // light, dark, system
            $table->string('color_scheme')->default('default'); // default, blue, green, etc.
            $table->string('font_size')->default('medium'); // small, medium, large
            $table->boolean('reduce_animations')->default(false);
            $table->boolean('high_contrast')->default(false);
            $table->timestamps();
            
            $table->unique('user_id');
        });
        
        // User privacy settings table
        Schema::create('user_privacy_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('online_status_visibility', ['everyone', 'contacts', 'nobody'])->default('everyone');
            $table->enum('last_seen_visibility', ['everyone', 'contacts', 'nobody'])->default('everyone');
            $table->enum('profile_photo_visibility', ['everyone', 'contacts', 'nobody'])->default('everyone');
            $table->boolean('read_receipts')->default(true);
            $table->boolean('typing_indicators')->default(true);
            $table->boolean('allow_search_by_phone')->default(true);
            $table->boolean('allow_search_by_name')->default(true);
            $table->timestamps();
            
            $table->unique('user_id');
        });
        
        // User notification settings table
        Schema::create('user_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('message_notifications')->default(true);
            $table->boolean('group_notifications')->default(true);
            $table->boolean('call_notifications')->default(true);
            $table->boolean('transaction_notifications')->default(true);
            $table->boolean('marketing_notifications')->default(false);
            $table->boolean('sound_enabled')->default(true);
            $table->boolean('vibration_enabled')->default(true);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            $table->timestamps();
            
            $table->unique('user_id');
        });
        
        // User language settings
        Schema::create('user_language_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('app_language')->default('ar'); // ar, en, etc.
            $table->string('content_translation')->default('off'); // off, auto, manual
            $table->string('date_format')->default('gregorian'); // gregorian, hijri
            $table->string('time_format')->default('24h'); // 12h, 24h
            $table->string('first_day_of_week')->default('saturday'); // saturday, sunday, monday
            $table->string('measurement_system')->default('metric'); // metric, imperial
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_language_settings');
        Schema::dropIfExists('user_notification_settings');
        Schema::dropIfExists('user_privacy_settings');
        Schema::dropIfExists('user_ui_settings');
    }
};
