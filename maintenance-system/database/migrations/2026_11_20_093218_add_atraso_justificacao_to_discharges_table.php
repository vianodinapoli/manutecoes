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
    Schema::table('descargas', function (Blueprint $table) {
        $table->string('atraso_motivo', 40)->nullable();          // 'fila_descarga' ou 'outro'
        $table->text('atraso_justificacao')->nullable();          // texto livre (quando é "outro")
        $table->foreignId('atraso_justificado_por')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('atraso_justificado_em')->nullable();
    });
}

public function down(): void
{
    Schema::table('descargas', function (Blueprint $table) {
        $table->dropConstrainedForeignId('atraso_justificado_por');
        $table->dropColumn(['atraso_motivo', 'atraso_justificacao', 'atraso_justificado_em']);
    });
}
};
