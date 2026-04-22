<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── 1. Todas as permissões ──
        $permissoes = [
            'acesso dashboard',
            'acesso equipamentos',
            'acesso manutencoes',
            'acesso stock',
            'acesso movimentos',
            'acesso pedidos',
            'acesso combustivel',
            'acesso discharges',
            'acesso viaturas',
            'acesso caixa',
            'adicionar registros',
            'editar status',
            'gerir utilizadores',
            'emitir requisicoes',
            'requisicoes-material',
        ];

        foreach ($permissoes as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ── 2. Roles ──

        // Super Admin — acesso total, único com tudo
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->syncPermissions(Permission::all());

        // Gestor — apenas dashboard por defeito, resto atribuído individualmente
        $gestor = Role::firstOrCreate(['name' => 'gestor']);
        $gestor->syncPermissions([
            'acesso dashboard',
        ]);

        // Utilizador — apenas dashboard por defeito, resto atribuído individualmente
        $utilizador = Role::firstOrCreate(['name' => 'utilizador']);
        $utilizador->syncPermissions([
            'acesso dashboard',
        ]);

        // ── 3. Super Admin padrão ──
        $admin = User::updateOrCreate(
            ['email' => 'admin@sistema.com'],
            [
                'name'     => 'Viano Admin',
                'password' => bcrypt('password'),
            ]
        );
        $admin->syncRoles(['super-admin']);

        $this->command->info('Roles e permissões criados com sucesso!');
    }
}