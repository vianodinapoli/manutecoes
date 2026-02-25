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
        $table->string('work_sheet_ref', 50)->nullable()->after('end_date');
        $table->unsignedInteger('hours_kms')->nullable()->after('work_sheet_ref');
        $table->decimal('total_cost', 10, 2)->default(0.00)->after('hours_kms');
        // Nota: Os campos MATRÌCULA, SÉRIE, Nª DE CHASSI já deveriam estar na tabela 'machines', não em 'maintenances'.
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('maintenances', function (Blueprint $table) {
        $table->dropColumn(['work_sheet_ref', 'hours_kms', 'total_cost']);
    });
    }
};
