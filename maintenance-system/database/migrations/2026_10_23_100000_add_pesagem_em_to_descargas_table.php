<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('descargas', function (Blueprint $t) {
            $t->dateTime('peso_bruto_em')->nullable()->after('peso_bruto');
            $t->dateTime('tara_em')->nullable()->after('tara');
        });

        // Se a coluna `status` foi criada como ENUM ('in_transit','confirmed','pending'),
        // o novo valor 'weighing' seria rejeitado. Descomente a linha abaixo (MySQL/MariaDB):
        // DB::statement("ALTER TABLE descargas MODIFY status VARCHAR(20) NOT NULL DEFAULT 'in_transit'");
    }

    public function down(): void
    {
        Schema::table('descargas', function (Blueprint $t) {
            $t->dropColumn(['peso_bruto_em', 'tara_em']);
        });
    }
};
