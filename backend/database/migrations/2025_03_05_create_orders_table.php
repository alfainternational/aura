<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تحديث جدول الطلبات وإنشاء جدول عناصر الطلب
     */
    public function up(): void
    {
        // التحقق من وجود جدول الطلبات وإنشائه إذا لم يكن موجودًا
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_number')->unique();
                $table->foreignId('user_id')->constrained();
                $table->foreignId('merchant_id')->nullable()->constrained('users');
                $table->foreignId('delivery_agent_id')->nullable()->constrained('users');
                $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'])->default('pending');
                $table->decimal('total_amount', 10, 2);
                $table->decimal('tax_amount', 10, 2)->default(0);
                $table->decimal('shipping_amount', 10, 2)->default(0);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->string('currency', 3)->default('SDG');
                $table->string('payment_method')->default('cash');
                $table->foreignId('shipping_method_id')->nullable();
                $table->string('shipping_address');
                $table->string('billing_address')->nullable();
                $table->string('contact_phone');
                $table->string('contact_email')->nullable();
                $table->dateTime('estimated_delivery_date')->nullable();
                $table->dateTime('actual_delivery_date')->nullable();
                $table->string('tracking_number')->nullable();
                $table->string('shipping_label_url')->nullable();
                $table->text('notes')->nullable();
                $table->text('customer_notes')->nullable();
                $table->text('admin_notes')->nullable();
                $table->boolean('is_gift')->default(false);
                $table->text('gift_message')->nullable();
                $table->string('source')->default('website');
                $table->json('device_info')->nullable();
                $table->json('meta_data')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        } else {
            // إضافة الأعمدة الجديدة إذا لم تكن موجودة
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'order_number')) {
                    $table->string('order_number')->unique();
                }
                if (!Schema::hasColumn('orders', 'merchant_id')) {
                    $table->foreignId('merchant_id')->nullable()->constrained('users');
                }
                if (!Schema::hasColumn('orders', 'delivery_agent_id')) {
                    $table->foreignId('delivery_agent_id')->nullable()->constrained('users');
                }
                if (!Schema::hasColumn('orders', 'shipping_method_id')) {
                    $table->foreignId('shipping_method_id')->nullable();
                }
                if (!Schema::hasColumn('orders', 'estimated_delivery_date')) {
                    $table->dateTime('estimated_delivery_date')->nullable();
                }
                if (!Schema::hasColumn('orders', 'actual_delivery_date')) {
                    $table->dateTime('actual_delivery_date')->nullable();
                }
                if (!Schema::hasColumn('orders', 'tracking_number')) {
                    $table->string('tracking_number')->nullable();
                }
                if (!Schema::hasColumn('orders', 'shipping_label_url')) {
                    $table->string('shipping_label_url')->nullable();
                }
                if (!Schema::hasColumn('orders', 'notes')) {
                    $table->text('notes')->nullable();
                }
                if (!Schema::hasColumn('orders', 'customer_notes')) {
                    $table->text('customer_notes')->nullable();
                }
                if (!Schema::hasColumn('orders', 'admin_notes')) {
                    $table->text('admin_notes')->nullable();
                }
                if (!Schema::hasColumn('orders', 'is_gift')) {
                    $table->boolean('is_gift')->default(false);
                }
                if (!Schema::hasColumn('orders', 'gift_message')) {
                    $table->text('gift_message')->nullable();
                }
                if (!Schema::hasColumn('orders', 'source')) {
                    $table->string('source')->default('website');
                }
                if (!Schema::hasColumn('orders', 'device_info')) {
                    $table->json('device_info')->nullable();
                }
                if (!Schema::hasColumn('orders', 'meta_data')) {
                    $table->json('meta_data')->nullable();
                }
                if (!Schema::hasColumn('orders', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
        
        // التحقق من وجود جدول عناصر الطلب وإنشائه إذا لم يكن موجودًا
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained();
                $table->string('product_name');
                $table->string('product_sku');
                $table->integer('quantity');
                $table->decimal('unit_price', 10, 2);
                $table->decimal('subtotal', 10, 2);
                $table->string('variant')->nullable();
                $table->json('options')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا نقوم بحذف الجداول بالكامل، فقط نزيل الأعمدة المضافة
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $columns = [
                    'order_number',
                    'merchant_id',
                    'delivery_agent_id',
                    'shipping_method_id',
                    'estimated_delivery_date',
                    'actual_delivery_date',
                    'tracking_number',
                    'shipping_label_url',
                    'notes',
                    'customer_notes',
                    'admin_notes',
                    'is_gift',
                    'gift_message',
                    'source',
                    'device_info',
                    'meta_data',
                    'deleted_at'
                ];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('orders', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
        
        Schema::dropIfExists('order_items');
    }
};
