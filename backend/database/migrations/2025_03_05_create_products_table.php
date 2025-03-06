<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تحديث جدول المنتجات بدلاً من إنشائه من جديد
     */
    public function up(): void
    {
        // تحقق من وجود عمود merchant_id
        if (!Schema::hasColumn('products', 'merchant_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('merchant_id')->after('id')->constrained('users');
            });
        }

        // تحقق من وجود عمود product_categories
        if (Schema::hasTable('product_categories') && !Schema::hasColumn('products', 'product_category_id')) {
            // إنشاء جدول فئات المنتجات إذا لم يكن موجوداً
            if (!Schema::hasTable('product_categories')) {
                Schema::create('product_categories', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('slug')->unique();
                    $table->text('description')->nullable();
                    $table->string('image')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });
            }

            // نقل البيانات من category_id إلى product_category_id
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('product_category_id')->after('category_id')->nullable();
            });
        }

        // إضافة أعمدة جديدة إذا لم تكن موجودة
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'short_description')) {
                $table->text('short_description')->nullable()->after('description');
            }
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->nullable()->after('sku');
            }
            if (!Schema::hasColumn('products', 'specifications')) {
                $table->json('specifications')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('products', 'options')) {
                $table->json('options')->nullable()->after('specifications');
            }
            if (!Schema::hasColumn('products', 'variants')) {
                $table->json('variants')->nullable()->after('options');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إزالة الأعمدة المضافة
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'short_description',
                'barcode',
                'specifications',
                'options',
                'variants',
                'product_category_id',
                'merchant_id'
            ]);
        });
    }
};
