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
        Schema::create('payments', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('order_id');

            $table->decimal('amount', 10, 2);

            $table->string('payment_method')->nullable(); // upi/card/manual
            $table->string('gateway_name')->nullable();

            $table->string('gateway_payment_id')->nullable();
            $table->string('gateway_signature')->nullable();

            $table->enum('status', ['pending', 'initiated', 'success', 'failed', 'refunded'])->default('initiated');

            $table->timestamp('paid_at')->nullable();

            $table->json('raw_response')->nullable(); // full gateway response (optional)

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
