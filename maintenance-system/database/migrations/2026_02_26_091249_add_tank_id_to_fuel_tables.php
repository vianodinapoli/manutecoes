<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adiciona tank_id na tabela de saídas
        Schema::table('fuel_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('tank_id')->nullable()->after('id');
        });

        // Adiciona tank_id na tabela de entradas
        Schema::table('fuel_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('tank_id')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('fuel_logs', function (Blueprint $table) {
            $table->dropColumn('tank_id');
        });
        Schema::table('fuel_entries', function (Blueprint $table) {
            $table->dropColumn('tank_id');
        });
    }
};