<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up() {
        Schema::table('budgets', function (Blueprint $table) {
            $table->boolean('is_automatic')->default(false);
            $table->json('allocation_percentages')->nullable(); // For automatic allocation percentages
        });
    }
    
    public function down() {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropColumn('is_automatic');
            $table->dropColumn('allocation_percentages');
        });
    }
};
