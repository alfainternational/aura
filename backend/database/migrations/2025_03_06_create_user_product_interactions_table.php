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
        // تحقق من وجود جدول تفاعلات المستخدم مع المنتجات
        if (!Schema::hasTable('user_product_interactions')) {
            Schema::create('user_product_interactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('interaction_type'); // view, add_to_cart, purchase, wishlist
                $table->json('metadata')->nullable();
                $table->timestamps();
                
                // Add index for faster queries
                $table->index(['user_id', 'interaction_type']);
                $table->index(['product_id', 'interaction_type']);
            });
        }
        
        // تحقق من وجود جدول العلاقات بين المنتجات
        if (!Schema::hasTable('product_relationships')) {
            Schema::create('product_relationships', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->foreignId('related_product_id')->constrained('products')->onDelete('cascade');
                $table->string('relationship_type'); // complementary, similar, upsell, etc.
                $table->integer('display_order')->default(0);
                $table->timestamps();
                
                // Add unique constraint to prevent duplicates with a shorter name
                $table->unique(['product_id', 'related_product_id', 'relationship_type'], 'unique_relationship');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_relationships');
        Schema::dropIfExists('user_product_interactions');
    }
};
