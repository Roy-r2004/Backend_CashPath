<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('budgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('category_id')->nullable(); // Nullable for automatic budgets
            $table->decimal('amount', 12, 2)->default(0);
            $table->enum('mode', ['Manual', 'Automatic'])->default('Manual'); // New
            $table->enum('period', ['Monthly', 'Weekly', 'Custom'])->default('Monthly');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('spent_amount', 12, 2)->default(0);
            $table->enum('status', ['Active', 'Expired'])->default('Active');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('budgets');
    }
};
