<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('profile_picture')->nullable();
            $table->string('currency', 10)->default('USD');
            $table->string('language', 20)->default('en');
            $table->uuid('default_account_id')->nullable();
            $table->string('timezone')->default('UTC');
            $table->rememberToken(); // "Remember Me" authentication token
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('users');
    }
};

