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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_price_id')->nullable()->constrained()->nullOnDelete();

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3); // BRL, USD

            $table->string('status');

            $table->string('payment_gateway')->nullable(); // stripe, manual
            $table->string('payment_method')->nullable();  // credit_card, pix, boleto

            $table->string('gateway_transaction_id')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};