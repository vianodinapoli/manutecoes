<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Lista de transportadoras da operação: [{"nome": "...", "extra": false}, ...]
        // extra = true → foi acrescentada ao lançar um camião (não estava prevista)
        Schema::table('discharge_operations', function (Blueprint $t) {
            if (! Schema::hasColumn('discharge_operations', 'transportadoras')) {
                $t->json('transportadoras')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('discharge_operations', function (Blueprint $t) {
            if (Schema::hasColumn('discharge_operations', 'transportadoras')) {
                $t->dropColumn('transportadoras');
            }
        });
    }
};
