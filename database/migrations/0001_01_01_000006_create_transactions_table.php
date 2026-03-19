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
            $table->foreignId('plan_price_id')->nullable();

            $table->decimal('amount', 10, 2);
            $table->decimal('amount_refunded', 10, 2)->nullable();
            $table->string('currency', 3);

            $table->string('status');

            $table->string('payment_gateway')->nullable();
            $table->string('payment_method')->nullable();

            $table->string('gateway_transaction_id')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->json('meta')->nullable(); // resposta do gateway

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
