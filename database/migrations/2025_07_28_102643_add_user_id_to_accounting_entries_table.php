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
        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id')->nullable();
        });

        // ربط كل القيود المحاسبية الحالية بالمستخدم الأول
        $firstUserId = DB::table('users')->min('id');
        if ($firstUserId) {
            DB::table('accounting_entries')->update(['user_id' => $firstUserId]);
        }

        // جعل العمود غير قابل لأن يكون null ثم إضافة القيد
        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
