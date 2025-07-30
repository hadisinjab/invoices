<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cash_boxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // اسم الصندوق
            $table->decimal('initial_balance', 10, 2)->default(0);
            $table->decimal('current_balance', 10, 2)->default(0); // الرصيد الحالي
            $table->decimal('last_calculated_balance', 10, 2)->default(0); // آخر رصيد محسوب من القيود
            $table->timestamp('last_calculation_date')->nullable(); // تاريخ آخر حساب للرصيد
            $table->text('notes')->nullable(); // ملاحظات
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_boxes');
    }
};
