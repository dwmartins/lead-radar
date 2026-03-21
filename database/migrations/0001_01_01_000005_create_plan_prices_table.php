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
        Schema::create('plan_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3); 
            $table->string('stripe_price_id')->nullable();
            $table->enum('type', ['recurring', 'one_time'])->default('recurring');
            $table->string('interval')->nullable();
            $table->integer('interval_count')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['plan_id', 'currency', 'type', 'interval', 'interval_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_prices');
    }
};
