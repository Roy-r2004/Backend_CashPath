<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('account_id');
            $table->uuid('category_id');
            $table->decimal('amount', 12, 2);
            $table->enum('type', ['Income', 'Expense']);
            $table->date('date');
            $table->time('time');
            $table->text('note')->nullable();
            $table->string('receipt_image')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('transactions');
    }
};
