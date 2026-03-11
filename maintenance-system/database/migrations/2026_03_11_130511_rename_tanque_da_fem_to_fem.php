<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tanks')
            ->where('nome', 'Tanque da Fem')
            ->update(['nome' => 'FEM']);
    }

    public function down(): void
    {
        DB::table('tanks')
            ->where('nome', 'FEM')
            ->update(['nome' => 'Tanque da Fem']);
    }
};