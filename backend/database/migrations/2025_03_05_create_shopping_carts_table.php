<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // تحقق من وجود جدول سلات التسوق
        if (Schema::hasTable('shopping_carts')) {
            Schema::table('shopping_carts', function (Blueprint $table) {
                if (!Schema::hasColumn('shopping_carts', 'status')) {
                    $table->string('status')->default('active');
                }
                if (!Schema::hasColumn('shopping_carts', 'abandoned_at')) {
                    $table->timestamp('abandoned_at')->nullable();
                }
                if (!Schema::hasColumn('shopping_carts', 'converted_at')) {
                    $table->timestamp('converted_at')->nullable();
                }
                
                // لا نضيف الفهرس هنا لأنه قد يكون موجودًا بالفعل
            });
        } else {
            Schema::create('shopping_carts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('session_id')->nullable();
                $table->string('status')->default('active');
                $table->timestamp('abandoned_at')->nullable();
                $table->timestamp('converted_at')->nullable();
                $table->timestamps();
                
                // إضافة فهرس للبحث السريع
                $table->index(['user_id', 'session_id', 'status']);
            });
        }
        
        // تحقق من وجود جدول عناصر سلة التسوق
        if (Schema::hasTable('cart_items') && !Schema::hasTable('shopping_cart_items')) {
            // بدلاً من إعادة تسمية الجدول، سنقوم بإنشاء جدول جديد ونقل البيانات
            Schema::create('shopping_cart_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shopping_cart_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->integer('quantity')->default(1);
                $table->decimal('price', 10, 2);
                $table->json('options')->nullable();
                $table->timestamps();
                
                // إضافة فهرس للبحث السريع
                $table->index(['shopping_cart_id', 'product_id']);
            });
            
            // نقل البيانات من الجدول القديم إلى الجدول الجديد إذا كان هناك بيانات
            try {
                $cartItems = DB::table('cart_items')->get();
                foreach ($cartItems as $item) {
                    DB::table('shopping_cart_items')->insert([
                        'shopping_cart_id' => $item->cart_id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->unit_price,
                        'options' => $item->options ?? null,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at
                    ]);
                }
                
                // حذف الجدول القديم بعد نقل البيانات
                Schema::dropIfExists('cart_items');
            } catch (\Exception $e) {
                // لا شيء، ربما الجدول فارغ أو لا يحتوي على الأعمدة المتوقعة
            }
        } else if (!Schema::hasTable('shopping_cart_items')) {
            Schema::create('shopping_cart_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shopping_cart_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->integer('quantity')->default(1);
                $table->decimal('price', 10, 2);
                $table->json('options')->nullable();
                $table->timestamps();
                
                // إضافة فهرس للبحث السريع
                $table->index(['shopping_cart_id', 'product_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_cart_items');
        Schema::dropIfExists('shopping_carts');
    }
};
