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
        Schema::create('messenger_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('national_id')->unique();
            $table->string('id_document');
            $table->string('driving_license');
            $table->string('license_document');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('zone_id')->nullable()->constrained()->onDelete('set null');
            $table->string('address');
            $table->string('city');
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->boolean('is_online')->default(false);
            $table->enum('delivery_preference', ['food', 'goods', 'both'])->default('both');
            $table->string('work_hours');
            $table->decimal('current_latitude', 10, 7)->nullable();
            $table->decimal('current_longitude', 10, 7)->nullable();
            $table->integer('completed_deliveries')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->timestamp('last_active_at')->nullable();
            $table->string('reference_code')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messenger_profiles');
    }
};
