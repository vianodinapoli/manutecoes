<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissoesModulosSeeder extends Seeder
{
    public function run(): void
    {
        // ── Limpar cache do Spatie ──
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Definir permissões por módulo ──
        $permissoes = [
            // Existentes (manter)
            'adicionar registros',
            'editar status',
            'gerir utilizadores',

            // Módulos novos
            'acesso dashboard',
            'acesso equipamentos',
            'acesso manutencoes',
            'acesso stock',
            'acesso movimentos',
            'acesso pedidos',
            'acesso combustivel',
            'acesso discharges',
        ];

        foreach ($permissoes as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // ── Super-admin: todas as permissões ──
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // ── Utilizador: permissões base ──
        $utilizador = Role::firstOrCreate(['name' => 'utilizador', 'guard_name' => 'web']);
        $utilizador->syncPermissions([
            'adicionar registros',
            'acesso dashboard',
            'acesso equipamentos',
            'acesso manutencoes',
            'acesso stock',
            'acesso movimentos',
        ]);

        $this->command->info('✅ Permissões criadas e atribuídas com sucesso!');
    }
}