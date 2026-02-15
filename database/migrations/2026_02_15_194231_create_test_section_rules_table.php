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
        Schema::create('test_section_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('test_section_id');
            $table->enum('difficulty', ['easy', 'medium', 'hard']);
            $table->integer('question_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_section_rules');
    }
};
