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
        Schema::table('users', function (Blueprint $table) {
            // إضافة الأدوار والصلاحيات
            $table->string('role')->nullable()->after('user_type');
            $table->json('permissions')->nullable()->after('role');
            
            // معلومات شخصية إضافية
            $table->date('birth_date')->nullable()->after('profile_image');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('birth_date');
            
            // حذف إضافة أعمدة المصادقة الثنائية هنا لأنها موجودة بالفعل
            // في هجرة 2023_03_02_000000_add_two_factor_auth_columns_to_users_table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'permissions',
                'birth_date',
                'gender'
                // تم حذف الأعمدة المتعلقة بالمصادقة الثنائية من هنا أيضًا
            ]);
        });
    }
};
