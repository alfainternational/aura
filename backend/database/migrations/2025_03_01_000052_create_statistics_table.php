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
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('period')->default('daily'); // hourly, daily, weekly, monthly, yearly, all_time
            $table->date('date');
            $table->integer('count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['key', 'entity_type', 'entity_id', 'period', 'date']);
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->json('parameters')->nullable();
            $table->json('data')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->text('error')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('statistics');
    }
};
