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
        Schema::create('agent_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('agency_name');
            $table->string('agency_logo')->nullable();
            $table->string('agency_type');
            $table->string('license_number')->nullable();
            $table->string('license_document')->nullable();
            $table->string('national_id');
            $table->string('id_document');
            $table->string('contact_email')->nullable();
            $table->string('contact_phone');
            $table->text('address');
            $table->string('city');
            $table->string('zone')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->text('service_areas')->nullable();
            $table->string('reference_code')->unique();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_profiles');
    }
};
