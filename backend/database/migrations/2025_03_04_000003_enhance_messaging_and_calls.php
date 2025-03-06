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
        // Enhance conversations table with more features
        Schema::table('conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('conversations', 'description')) {
                $table->string('description')->nullable();
            }
            if (!Schema::hasColumn('conversations', 'icon')) {
                $table->string('icon')->nullable();
            }
            if (!Schema::hasColumn('conversations', 'pinned_message_id')) {
                $table->foreignId('pinned_message_id')->nullable()->constrained('messages')->nullOnDelete();
            }
            if (!Schema::hasColumn('conversations', 'settings')) {
                $table->json('settings')->nullable();
            }
        });
        
        // Enhance message attachments
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['image', 'video', 'audio', 'file', 'location', 'contact', 'link']);
            $table->string('file_name')->nullable();
            $table->string('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('file_path')->nullable();
            $table->text('thumbnail_path')->nullable();
            $table->decimal('location_latitude', 10, 8)->nullable();
            $table->decimal('location_longitude', 11, 8)->nullable();
            $table->string('location_name')->nullable();
            $table->text('metadata')->nullable(); // For additional metadata
            $table->timestamps();
        });
        
        // Create link previews table for rich link previews in messages
        Schema::create('link_previews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->string('url');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('favicon')->nullable();
            $table->string('domain')->nullable();
            $table->timestamps();
        });
        
        // Enhance the voice_calls table
        Schema::table('voice_calls', function (Blueprint $table) {
            if (!Schema::hasColumn('voice_calls', 'recording_path')) {
                $table->string('recording_path')->nullable();
            }
            if (!Schema::hasColumn('voice_calls', 'transcription')) {
                $table->text('transcription')->nullable();
            }
            if (!Schema::hasColumn('voice_calls', 'quality_metrics')) {
                $table->json('quality_metrics')->nullable(); // For call quality data
            }
            if (!Schema::hasColumn('voice_calls', 'max_participants')) {
                $table->integer('max_participants')->default(8);
            }
        });
        
        // Voice messages table
        if (!Schema::hasTable('voice_messages')) {
            Schema::create('voice_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('message_id')->constrained()->onDelete('cascade');
                $table->string('duration'); // Duration in seconds
                $table->string('file_path');
                $table->text('transcription')->nullable(); // For automatic speech-to-text
                $table->boolean('is_listened')->default(false);
                $table->timestamps();
            });
        }
        
        // Message translations table
        Schema::create('message_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->string('source_language');
            $table->string('target_language');
            $table->text('translated_content');
            $table->timestamp('translated_at');
            $table->timestamps();
            
            $table->unique(['message_id', 'target_language']);
        });
        
        // Conversation participant settings (for muting, notification preferences, etc.)
        Schema::table('conversation_participants', function (Blueprint $table) {
            if (!Schema::hasColumn('conversation_participants', 'muted_until')) {
                $table->timestamp('muted_until')->nullable();
            }
            if (!Schema::hasColumn('conversation_participants', 'notification_level')) {
                $table->enum('notification_level', ['all', 'mentions', 'none'])->default('all');
            }
            if (!Schema::hasColumn('conversation_participants', 'custom_name')) {
                $table->string('custom_name')->nullable(); // Custom name for contact/group
            }
            if (!Schema::hasColumn('conversation_participants', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false); // Pin conversation to top
            }
            if (!Schema::hasColumn('conversation_participants', 'last_read_message_id')) {
                $table->foreignId('last_read_message_id')->nullable()->constrained('messages')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_translations');
        
        if (Schema::hasTable('voice_messages')) {
            Schema::dropIfExists('voice_messages');
        }
        
        Schema::dropIfExists('link_previews');
        Schema::dropIfExists('message_attachments');
        
        Schema::table('conversation_participants', function (Blueprint $table) {
            $columns = [
                'muted_until', 'notification_level', 'custom_name', 
                'is_pinned', 'last_read_message_id'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('conversation_participants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
        
        Schema::table('voice_calls', function (Blueprint $table) {
            $columns = [
                'recording_path', 'transcription', 'quality_metrics', 
                'max_participants'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('voice_calls', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
        
        Schema::table('conversations', function (Blueprint $table) {
            $columns = [
                'description', 'icon', 'pinned_message_id', 'settings'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('conversations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
