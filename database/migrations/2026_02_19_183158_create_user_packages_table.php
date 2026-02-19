<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_packages', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relations
            |--------------------------------------------------------------------------
            */
            $table->unsignedBigInteger('user_id');

            $table->unsignedBigInteger('package_id');

            $table->unsignedBigInteger('order_id');

            /*
            |--------------------------------------------------------------------------
            | Activation Info
            |--------------------------------------------------------------------------
            */
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expiry_date')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */
            $table->enum('status', ['active', 'expired', 'cancelled'])
                ->default('active');

            /*
            |--------------------------------------------------------------------------
            | Attempt Tracking (Future Ready)
            |--------------------------------------------------------------------------
            */
            $table->integer('attempts_used')->default(0);
            $table->integer('max_attempts')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Unique Constraint
            |--------------------------------------------------------------------------
            */
            $table->unique(['user_id', 'package_id', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_packages');
    }
};
