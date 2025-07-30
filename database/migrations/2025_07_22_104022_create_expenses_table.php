<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for the expenses table.
 * يحتوي على مصاريف المستخدمين (اسم المستلزم، سعره، تاريخه، وعلاقته بالمستخدم)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المستلزم
            $table->decimal('amount', 10, 2); // سعر المستلزم
            $table->date('expense_date'); // تاريخ المستلزم
            $table->text('comment')->nullable(); // تعليق على المصروف
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // علاقة مع المستخدم
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
