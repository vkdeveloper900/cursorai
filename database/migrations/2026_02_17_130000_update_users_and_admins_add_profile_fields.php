<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->date('dob')->nullable()->after('last_name');
            $table->string('phone', 20)->nullable()->after('dob');
            $table->string('status', 20)->default('active')->after('remember_token');
            $table->string('social_provider')->nullable()->after('status');
            $table->string('social_id')->nullable()->after('social_provider');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->after('id')->default(1);
            $table->string('first_name')->nullable()->after('role_id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('mobile', 20)->nullable()->after('last_name');
            $table->string('status', 20)->default('active')->after('mobile');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'dob',
                'phone',
                'status',
                'social_provider',
                'social_id',
            ]);
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'mobile',
                'status',
            ]);
        });
    }
};

