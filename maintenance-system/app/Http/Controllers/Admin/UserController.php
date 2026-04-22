<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    private array $modulos = [
        'acesso dashboard'   => ['label' => 'Dashboard',          'icon' => 'fa-home'],
        'acesso equipamentos'=> ['label' => 'Equipamentos',       'icon' => 'fa-tools'],
        'acesso manutencoes' => ['label' => 'Manutenções',        'icon' => 'fa-wrench'],
        'acesso stock'       => ['label' => 'Stock',              'icon' => 'fa-boxes'],
        'acesso movimentos'  => ['label' => 'Movimentos Armazém', 'icon' => 'fa-arrow-right-arrow-left'],
        'acesso pedidos'     => ['label' => 'Pedidos/Requisições','icon' => 'fa-shopping-cart'],
        'acesso combustivel' => ['label' => 'Combustível',        'icon' => 'fa-gas-pump'],
        'acesso viaturas'    => ['label' => 'Viaturas',           'icon' => 'fa-truck-moving'],
        'acesso discharges'  => ['label' => 'Discharges',         'icon' => 'fa-sign-out-alt'],
        'acesso caixa'       => ['label' => 'Caixa e Bancos',     'icon' => 'fa-cash-register'],
        'emitir requisicoes' => ['label' => 'Emitir Requisições', 'icon' => 'fa-file-arrow-up'],
        'requisicoes-material' => ['label' => 'Requisições de Material', 'icon' => 'fa-dolly'],
    ];

    public function index()
    {
        $users = User::with('roles', 'permissions')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', [
            'users'   => $users,
            'roles'   => $roles,
            'modulos' => $this->modulos,
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'name'         => 'required|string|max:255',
        'email'        => 'required|email|unique:users,email',
        'password'     => 'required|string|min:8|confirmed',
        'role'         => 'required|string|exists:roles,name',
        'departamento' => 'nullable|string|max:255',
        'funcao'       => 'nullable|string|max:255',
    ]);

    $user = User::create([
        'name'         => $request->name,
        'email'        => $request->email,
        'password'     => Hash::make($request->password),
        'departamento' => $request->departamento,
        'funcao'       => $request->funcao,
    ]);

    $user->syncRoles([$request->role]);

    if ($request->role !== 'super-admin') {
        $perms = array_intersect(
            $request->input('permissoes', []),
            array_keys($this->modulos)
        );
        $user->syncPermissions($perms);
    }

    return redirect()->route('admin.users.index')
        ->with('success', "Utilizador {$user->name} criado com sucesso.");
}

public function update(Request $request, User $user)
{
    $request->validate([
        'name'         => 'required|string|max:255',
        'email'        => 'required|email|unique:users,email,' . $user->id,
        'password'     => 'nullable|string|min:8|confirmed',
        'role'         => 'required|string|exists:roles,name',
        'departamento' => 'nullable|string|max:255',
        'funcao'       => 'nullable|string|max:255',
    ]);

    $user->update([
        'name'         => $request->name,
        'email'        => $request->email,
        'departamento' => $request->departamento,
        'funcao'       => $request->funcao,
        ...($request->filled('password')
            ? ['password' => Hash::make($request->password)]
            : []),
    ]);

    $user->syncRoles([$request->role]);

    if ($request->role !== 'super-admin') {
        $perms = array_intersect(
            $request->input('permissoes', []),
            array_keys($this->modulos)
        );
        $user->syncPermissions($perms);
    } else {
        $user->syncPermissions([]);
    }

    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    return redirect()->route('admin.users.index')
        ->with('success', "Utilizador {$user->name} actualizado.");
}

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Não podes eliminar a tua própria conta.');
        }

        $nome = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Utilizador {$nome} eliminado.");
    }

    public function toggleAdmin(User $user)
    {
        if ($user->hasRole('super-admin')) {
            $user->removeRole('super-admin');
            $user->assignRole('utilizador');
        } else {
            $user->syncRoles(['super-admin']);
            $user->syncPermissions([]);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.users.index')
            ->with('success', "Role de {$user->name} actualizado.");
    }
}