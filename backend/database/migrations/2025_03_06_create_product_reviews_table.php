<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * تحديث جدول مراجعات المنتجات بدلاً من إنشائه من جديد
     */
    public function up(): void
    {
        // التحقق من وجود الجدول وإنشائه إذا لم يكن موجودًا
        if (!Schema::hasTable('product_reviews')) {
            Schema::create('product_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained();
                $table->foreignId('product_id')->constrained();
                $table->string('title')->nullable();
                $table->integer('rating');
                // إزالة عمود التعليق
                // $table->text('comment')->nullable();
                $table->text('pros')->nullable();
                $table->text('cons')->nullable();
                $table->boolean('verified_purchase')->default(false);
                $table->integer('helpful_votes')->default(0);
                $table->integer('unhelpful_votes')->default(0);
                $table->json('images')->nullable();
                $table->string('ai_sentiment')->nullable();
                $table->json('ai_keywords')->nullable();
                $table->boolean('approved')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        } else {
            // إضافة أعمدة جديدة إذا لم تكن موجودة
            Schema::table('product_reviews', function (Blueprint $table) {
                if (!Schema::hasColumn('product_reviews', 'title')) {
                    $table->string('title')->nullable()->after('product_id');
                }
                if (!Schema::hasColumn('product_reviews', 'pros')) {
                    $table->text('pros')->nullable()->after('title');
                }
                if (!Schema::hasColumn('product_reviews', 'cons')) {
                    $table->text('cons')->nullable()->after('pros');
                }
                if (!Schema::hasColumn('product_reviews', 'verified_purchase')) {
                    $table->boolean('verified_purchase')->default(false)->after('cons');
                }
                if (!Schema::hasColumn('product_reviews', 'helpful_votes')) {
                    $table->integer('helpful_votes')->default(0)->after('verified_purchase');
                }
                if (!Schema::hasColumn('product_reviews', 'unhelpful_votes')) {
                    $table->integer('unhelpful_votes')->default(0)->after('helpful_votes');
                }
                if (!Schema::hasColumn('product_reviews', 'images')) {
                    $table->json('images')->nullable()->after('unhelpful_votes');
                }
                if (!Schema::hasColumn('product_reviews', 'ai_sentiment')) {
                    $table->string('ai_sentiment')->nullable()->after('images');
                }
                if (!Schema::hasColumn('product_reviews', 'ai_keywords')) {
                    $table->json('ai_keywords')->nullable()->after('ai_sentiment');
                }
                if (!Schema::hasColumn('product_reviews', 'deleted_at')) {
                    $table->softDeletes();
                }
                // إضافة عمود التعليق
                if (Schema::hasColumn('product_reviews', 'comment')) {
                    $table->dropColumn('comment');
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
        if (Schema::hasTable('product_reviews')) {
            Schema::table('product_reviews', function (Blueprint $table) {
                $columns = [
                    'title',
                    'pros',
                    'cons',
                    'verified_purchase',
                    'helpful_votes',
                    'unhelpful_votes',
                    'images',
                    'ai_sentiment',
                    'ai_keywords',
                    'deleted_at'
                ];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('product_reviews', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
