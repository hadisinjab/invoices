<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->enum('type', ['بيع', 'شراء', 'مردودات بيع', 'مردودات شراء']);
            $table->enum('status', ['مقبوضة', 'غير مقبوضة']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total_after_discount', 10, 2)->default(0);
            $table->string('received_currency')->nullable();
            $table->decimal('exchange_rate', 10, 4)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
