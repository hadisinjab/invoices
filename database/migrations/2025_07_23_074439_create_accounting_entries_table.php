<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounting_entries', function (Blueprint $table) {
            $table->id();

            // المصدر - الفاتورة أو المصروف
            $table->morphs('sourceable');

            // نوع العملية (قبض/دفع)
            $table->enum('operation_type', ['قبض', 'دفع']);

            // وصف العملية (بيع، شراء، مردودات بيع، مردودات شراء، مصروف)
            $table->string('operation_description');

            // المبلغ الإجمالي
            $table->decimal('amount', 10, 2);

            // حالة القبض
            $table->boolean('is_received')->default(false);

            // الطرف المدين
            $table->string('debtor_type')->nullable(); // صندوقي، صندوق العميل، منتجات
            $table->unsignedBigInteger('debtor_id')->nullable();

            // الطرف الدائن
            $table->string('creditor_type')->nullable(); // صندوقي، صندوق العميل، منتجات
            $table->unsignedBigInteger('creditor_id')->nullable();

            // التاريخ
            $table->timestamp('entry_date');

            // الملاحظات
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes - morphs() already creates the sourceable index
            $table->index('operation_type');
            $table->index('is_received');
            $table->index('entry_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounting_entries');
    }
};
