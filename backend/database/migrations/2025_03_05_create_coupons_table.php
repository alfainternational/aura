<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تحديث جدول الكوبونات بدلاً من إنشائه من جديد
     */
    public function up(): void
    {
        // التحقق من وجود الجدول وإنشائه إذا لم يكن موجودًا
        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('merchant_id')->nullable()->constrained('users');
                $table->string('code')->unique();
                $table->string('description')->nullable();
                $table->enum('type', ['percentage', 'fixed_amount', 'free_shipping'])->default('percentage');
                $table->decimal('value', 10, 2);
                $table->integer('usage_limit')->nullable();
                $table->integer('usage_limit_per_user')->nullable();
                $table->decimal('minimum_spend', 10, 2)->nullable();
                $table->decimal('minimum_order_amount', 10, 2)->nullable();
                $table->decimal('maximum_discount_amount', 10, 2)->nullable();
                $table->timestamp('start_date')->nullable();
                $table->timestamp('end_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_first_order_only')->default(false);
                $table->json('applicable_products')->nullable();
                $table->json('applicable_categories')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        } else {
            // إضافة أعمدة جديدة إذا لم تكن موجودة
            Schema::table('coupons', function (Blueprint $table) {
                if (!Schema::hasColumn('coupons', 'merchant_id')) {
                    $table->foreignId('merchant_id')->nullable()->constrained('users');
                }
                if (!Schema::hasColumn('coupons', 'usage_limit_per_user')) {
                    $table->integer('usage_limit_per_user')->nullable();
                }
                if (!Schema::hasColumn('coupons', 'minimum_order_amount')) {
                    $table->decimal('minimum_order_amount', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('coupons', 'maximum_discount_amount')) {
                    $table->decimal('maximum_discount_amount', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('coupons', 'is_first_order_only')) {
                    $table->boolean('is_first_order_only')->default(false);
                }
                if (!Schema::hasColumn('coupons', 'applicable_products')) {
                    $table->json('applicable_products')->nullable();
                }
                if (!Schema::hasColumn('coupons', 'applicable_categories')) {
                    $table->json('applicable_categories')->nullable();
                }
                if (!Schema::hasColumn('coupons', 'deleted_at')) {
                    $table->softDeletes();
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
        if (Schema::hasTable('coupons')) {
            Schema::table('coupons', function (Blueprint $table) {
                $columns = [
                    'merchant_id',
                    'usage_limit_per_user',
                    'minimum_order_amount',
                    'maximum_discount_amount',
                    'is_first_order_only',
                    'applicable_products',
                    'applicable_categories',
                    'deleted_at'
                ];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('coupons', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
