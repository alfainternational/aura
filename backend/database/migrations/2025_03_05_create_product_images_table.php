<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تحديث جدول صور المنتجات بدلاً من إنشائه من جديد
     */
    public function up(): void
    {
        // التحقق من وجود الجدول وإنشائه إذا لم يكن موجودًا
        if (!Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('image_path');
                $table->string('alt_text')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_thumbnail')->default(false);
                $table->boolean('is_featured')->default(false);
                $table->json('meta_data')->nullable();
                $table->timestamps();
            });
        } else {
            // إضافة أعمدة جديدة إذا لم تكن موجودة
            Schema::table('product_images', function (Blueprint $table) {
                if (!Schema::hasColumn('product_images', 'alt_text')) {
                    $table->string('alt_text')->nullable()->after('image_path');
                }
                if (!Schema::hasColumn('product_images', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('alt_text');
                }
                if (!Schema::hasColumn('product_images', 'is_thumbnail')) {
                    $table->boolean('is_thumbnail')->default(false)->after('sort_order');
                }
                if (!Schema::hasColumn('product_images', 'is_featured')) {
                    $table->boolean('is_featured')->default(false)->after('is_thumbnail');
                }
                if (!Schema::hasColumn('product_images', 'meta_data')) {
                    $table->json('meta_data')->nullable()->after('is_featured');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا نقوم بحذف الجدول بالكامل، فقط نزيل الأعمدة المضافة
        if (Schema::hasTable('product_images')) {
            Schema::table('product_images', function (Blueprint $table) {
                $columns = [
                    'alt_text',
                    'sort_order',
                    'is_thumbnail',
                    'is_featured',
                    'meta_data'
                ];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('product_images', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
