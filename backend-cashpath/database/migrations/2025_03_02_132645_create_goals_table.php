<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('goals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('name');
            $table->decimal('target_amount', 12, 2);
            $table->decimal('saved_amount', 12, 2)->default(0);
            $table->date('deadline')->nullable();
            $table->enum('status', ['In Progress', 'Achieved', 'Canceled']);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('goals');
    }
};
