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
        Schema::table('cash_boxes', function (Blueprint $table) {
            if (!Schema::hasColumn('cash_boxes', 'initial_balance')) {
                $table->decimal('initial_balance', 10, 2)->default(0)->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_boxes', function (Blueprint $table) {
            $table->dropColumn('initial_balance');
        });
    }
};
