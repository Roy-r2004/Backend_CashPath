<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('base_currency', 10);
            $table->string('target_currency', 10);
            $table->decimal('exchange_rate', 12, 5);
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('currency_rates');
    }
};

