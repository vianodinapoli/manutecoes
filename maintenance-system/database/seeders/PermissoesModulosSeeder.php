<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissoesModulosSeeder extends Seeder  // ← mantém o nome original
{
    public function run(): void
    {
        $permissoes = [
            'acesso dashboard',
            'acesso equipamentos',
            'acesso manutencoes',
            'acesso stock',
            'acesso movimentos',
            'acesso pedidos',
            'acesso combustivel',
            'acesso viaturas',
            'acesso discharges',
            'acesso caixa',
        ];

        foreach ($permissoes as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'utilizador', 'guard_name' => 'web']);

        // Super-admin recebe todas as permissões
        $superAdmin->syncPermissions(Permission::all());
    }
}