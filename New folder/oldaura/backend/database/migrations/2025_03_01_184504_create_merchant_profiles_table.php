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
        Schema::create('merchant_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('store_name');
            $table->string('store_logo')->nullable();
            $table->string('store_banner')->nullable();
            $table->text('store_description')->nullable();
            $table->string('business_type');
            $table->string('business_registration_number')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('registration_document')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone');
            $table->string('website')->nullable();
            $table->text('address');
            $table->string('city');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_open')->default(false);
            $table->string('opening_hours')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('minimum_order', 8, 2)->default(0);
            $table->boolean('free_delivery')->default(false);
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_profiles');
    }
};
