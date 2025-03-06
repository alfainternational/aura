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
        Schema::create('voice_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('caller_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('initiated'); // initiated, ongoing, completed, missed, declined
            $table->string('call_type')->default('voice'); // voice, video
            $table->string('channel_name')->nullable();
            $table->string('token')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration')->nullable(); // in seconds
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('voice_call_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voice_call_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('invited'); // invited, ringing, joined, left, declined, missed
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('ringing_at')->nullable();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();

            $table->unique(['voice_call_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voice_call_participants');
        Schema::dropIfExists('voice_calls');
    }
};
