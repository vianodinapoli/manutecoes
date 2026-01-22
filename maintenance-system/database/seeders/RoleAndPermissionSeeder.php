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
        // Limpar cache de permissões (Sempre boa prática)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Criar as Permissões específicas
        Permission::create(['name' => 'adicionar registros']);
        Permission::create(['name' => 'editar status']);
        Permission::create(['name' => 'gerir utilizadores']);

        // 2. Criar os Papéis (Roles) e atribuir permissões
        
        // Super Admin: Pode tudo
        $roleSuperAdmin = Role::create(['name' => 'super-admin']);
        $roleSuperAdmin->givePermissionTo(Permission::all());

        // Utilizador Comum: Só pode adicionar
        $roleUser = Role::create(['name' => 'utilizador']);
        $roleUser->givePermissionTo('adicionar registros');

        // 3. Criar o teu utilizador Super Admin para teste
        $admin = User::updateOrCreate(
            ['email' => 'admin@sistema.com'],
            [
                'name' => 'Viano Admin',
                'password' => bcrypt('password'), // Altera isto depois!
            ]
        );
        $admin->assignRole($roleSuperAdmin);

        $this->command->info('Sucesso: Papéis e Super-Admin criados!');
    }
}