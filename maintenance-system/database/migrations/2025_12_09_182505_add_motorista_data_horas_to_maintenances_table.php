<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            // Adiciona o nome do motorista/operador (string)
            $table->string('nome_motorista', 255)->nullable()->after('machine_id'); 
            
            // Adiciona a data de entrada na manutenção (date)
            $table->date('data_entrada')->nullable()->after('nome_motorista');
            
            // Adiciona as horas totais de trabalho (decimal para precisão, 8 dígitos no total, 2 após a vírgula)
            $table->decimal('horas_trabalho', 8, 2)->nullable()->after('data_entrada'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            // Reverte a ação removendo as colunas
            $table->dropColumn(['nome_motorista', 'data_entrada', 'horas_trabalho']);
        });
    }
};