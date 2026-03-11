<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tanks', function (Blueprint $table) {
            $table->string('owner')->nullable();
        });

        // Associar cada tanque ao seu dono
        DB::table('tanks')->where('nome', 'FEM')->update(['owner' => 'FEM']);
        DB::table('tanks')->where('nome', 'BYMOZE')->update(['owner' => 'BYMOZE']);
        DB::table('tanks')->where('nome', 'NITRO')->update(['owner' => 'NITRO']);
        DB::table('tanks')->where('nome', 'BOMBA MÓVEL')->update(['owner' => 'BOMBA MÓVEL']);
    }

    public function down(): void
    {
        Schema::table('tanks', function (Blueprint $table) {
            $table->dropColumn('owner');
        });
    }
};