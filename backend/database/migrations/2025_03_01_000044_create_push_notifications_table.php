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
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('image_url')->nullable();
            $table->string('action_url')->nullable();
            $table->json('data')->nullable();
            $table->string('type')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('push_notification_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('push_notification_id')->constrained()->onDelete('cascade');
            $table->enum('target_type', ['user', 'topic', 'device', 'all']);
            $table->string('target_id')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->string('status')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_notification_targets');
        Schema::dropIfExists('push_notifications');
    }
};
