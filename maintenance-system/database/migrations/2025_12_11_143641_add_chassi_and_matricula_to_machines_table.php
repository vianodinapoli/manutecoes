<?php

// database/migrations/2025_..._add_chassi_and_matricula_to_machines_table.php

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
        Schema::table('machines', function (Blueprint $table) {
            // Adiciona a coluna Matrícula (Opcional)
            $table->string('matricula', 50)->nullable()->after('numero_interno');
            
            // Adiciona a coluna Número de Chassi (Opcional)
            $table->string('nr_chassi', 100)->nullable()->after('matricula');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            // Remove as colunas em caso de rollback
            $table->dropColumn(['matricula', 'nr_chassi']);
        });
    }
};
