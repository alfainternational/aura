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
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voice_call_id')->constrained()->onDelete('cascade');
            $table->string('event_type'); // initiated, ringing, answered, ended, missed, declined
            $table->timestamp('event_time');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('call_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voice_call_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->integer('duration')->comment('Duration in seconds');
            $table->string('file_size')->nullable();
            $table->string('mime_type')->default('audio/mp3');
            $table->boolean('is_transcribed')->default(false);
            $table->text('transcription')->nullable();
            $table->timestamps();
        });

        Schema::create('call_quality_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voice_call_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->float('packet_loss')->nullable();
            $table->float('jitter')->nullable();
            $table->float('latency')->nullable();
            $table->integer('audio_quality_score')->nullable();
            $table->json('detailed_metrics')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_quality_metrics');
        Schema::dropIfExists('call_recordings');
        Schema::dropIfExists('call_logs');
    }
};
