<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_items') && ! Schema::hasColumn('stock_items', 'nome')) {
            Schema::table('stock_items', function (Blueprint $table) {
                $table->string('nome')->default('');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('stock_items') && Schema::hasColumn('stock_items', 'nome')) {
            Schema::table('stock_items', function (Blueprint $table) {
                $table->dropColumn('nome');
            });
        }
    }
};