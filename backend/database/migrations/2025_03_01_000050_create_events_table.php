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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->boolean('is_all_day')->default(false);
            $table->string('recurrence')->nullable(); // none, daily, weekly, monthly, yearly
            $table->json('recurrence_meta')->nullable();
            $table->string('status')->default('active'); // active, cancelled, completed
            $table->string('visibility')->default('private'); // private, public
            $table->string('color')->nullable();
            $table->json('reminders')->nullable();
            $table->timestamps();
        });

        Schema::create('event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->string('status')->default('pending'); // pending, accepted, declined, tentative
            $table->timestamp('responded_at')->nullable();
            $table->text('response_message')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
            $table->unique(['event_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_attendees');
        Schema::dropIfExists('events');
    }
};
