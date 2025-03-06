<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReplyForwardPinToMessagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('replied_to_id')->nullable()->after('deleted_at');
            $table->unsignedBigInteger('forwarded_from_id')->nullable()->after('replied_to_id');
            $table->boolean('is_pinned')->default(false)->after('forwarded_from_id');
            $table->timestamp('pinned_at')->nullable()->after('is_pinned');
            $table->unsignedBigInteger('pinned_by')->nullable()->after('pinned_at');
            
            // إضافة المفاتيح الأجنبية
            $table->foreign('replied_to_id')->references('id')->on('messages')->onDelete('set null');
            $table->foreign('forwarded_from_id')->references('id')->on('messages')->onDelete('set null');
            $table->foreign('pinned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['replied_to_id']);
            $table->dropForeign(['forwarded_from_id']);
            $table->dropForeign(['pinned_by']);
            
            $table->dropColumn('replied_to_id');
            $table->dropColumn('forwarded_from_id');
            $table->dropColumn('is_pinned');
            $table->dropColumn('pinned_at');
            $table->dropColumn('pinned_by');
        });
    }
}
