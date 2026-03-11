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
        Schema::table('menu_items', function (Blueprint $table) {
            $table->integer('daily_target_stock')->default(50)->after('price');
            $table->integer('current_stock')->default(50)->after('daily_target_stock');
            // We can drop expiration_date and stock_count later if desired, 
            // but for now let's just add the new fields.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['daily_target_stock', 'current_stock']);
        });
    }
};
