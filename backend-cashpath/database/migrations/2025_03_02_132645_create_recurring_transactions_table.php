<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('account_id');
            $table->uuid('category_id');
            $table->decimal('amount', 12, 2);
            $table->enum('type', ['Income', 'Expense']);
            $table->enum('interval', ['Daily', 'Weekly', 'Monthly', 'Yearly']);
            $table->date('next_due_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['Active', 'Paused', 'Canceled']);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('recurring_transactions');
    }
};
