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
    if (Schema::hasTable('payments')) {
        return;
    }
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->onDelete('cascade');
        $table->enum('method', ['cod', 'gcash', 'paymaya']);
        $table->decimal('amount', 10, 2);
        $table->string('reference_number')->nullable();
        $table->string('proof_image')->nullable(); // para sa gcash/paymaya
        $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
