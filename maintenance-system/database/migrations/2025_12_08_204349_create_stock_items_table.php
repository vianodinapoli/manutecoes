<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('stock_items')) {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS "stock_items_referencia_unique"');
        DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS "stock_items_ref_marca_unique" ON "stock_items" ("referencia", "marca_fabricante")');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS "stock_items_ref_marca_unique"');
        DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS "stock_items_referencia_unique" ON "stock_items" ("referencia")');
    }
};