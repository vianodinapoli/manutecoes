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
            // Acesso a módulos
            'acesso dashboard',
            'acesso equipamentos',
            'acesso manutencoes',
            'acesso stock',
            'acesso movimentos',
            'acesso pedidos',
            'acesso combustivel',
            'acesso discharges',
            // Acções
            'adicionar registros',
            'editar status',
            'gerir utilizadores',
        ];

        foreach ($permissoes as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ── 2. Roles ──

        // Super Admin — acesso total
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->syncPermissions(Permission::all());

        // Gestor — acesso a tudo excepto gerir utilizadores
        $gestor = Role::firstOrCreate(['name' => 'gestor']);
        $gestor->syncPermissions([
            'acesso dashboard',
            'acesso equipamentos',
            'acesso manutencoes',
            'acesso stock',
            'acesso movimentos',
            'acesso pedidos',
            'acesso combustivel',
            'acesso discharges',
            'adicionar registros',
            'editar status',
        ]);

        // Utilizador comum — acesso básico
        $utilizador = Role::firstOrCreate(['name' => 'utilizador']);
        $utilizador->syncPermissions([
            'acesso dashboard',
            'acesso equipamentos',
            'acesso manutencoes',
            'acesso stock',
            'acesso pedidos',
            'adicionar registros',
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