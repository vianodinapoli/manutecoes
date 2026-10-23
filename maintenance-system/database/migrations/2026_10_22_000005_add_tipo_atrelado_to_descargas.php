<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tipo de atrelado definido no porto: 'normal' (até 24 sacos) | 'link' (mais de 24 sacos)
        Schema::table('descargas', function (Blueprint $t) {
            if (! Schema::hasColumn('descargas', 'tipo_atrelado')) {
                $t->string('tipo_atrelado', 10)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('descargas', function (Blueprint $t) {
            if (Schema::hasColumn('descargas', 'tipo_atrelado')) {
                $t->dropColumn('tipo_atrelado');
            }
        });
    }
};
