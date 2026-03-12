<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private function modulos(): array
    {
        return [
            'acesso dashboard'    => ['label' => 'Dashboard',            'icon' => 'fa-home'],
            'acesso equipamentos' => ['label' => 'Equipamentos/Máquinas','icon' => 'fa-tools'],
            'acesso manutencoes'  => ['label' => 'Manutenções',          'icon' => 'fa-wrench'],
            'acesso stock'        => ['label' => 'Stock',                'icon' => 'fa-boxes'],
            'acesso movimentos'   => ['label' => 'Movimentos Armazém',   'icon' => 'fa-arrow-left-right'],
            'acesso pedidos'      => ['label' => 'Pedidos/Requisições',  'icon' => 'fa-shopping-cart'],
            'acesso combustivel'  => ['label' => 'Combustível',          'icon' => 'fa-gas-pump'],
            'acesso discharges'   => ['label' => 'Discharges',           'icon' => 'fa-sign-out-alt'],
            'adicionar registros' => ['label' => 'Adicionar Registos',   'icon' => 'fa-plus-circle'],
            'editar status'       => ['label' => 'Editar Status',        'icon' => 'fa-edit'],
            'gerir utilizadores'  => ['label' => 'Gerir Utilizadores',   'icon' => 'fa-users-cog'],
        ];
    }

    public function index()
    {
        $users   = User::with('roles', 'permissions')->orderBy('name')->get();
        $modulos = $this->modulos();
        $roles   = Role::orderBy('name')->get();
        return view('admin.users.index', compact('users', 'modulos', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
            'role'         => ['required', 'exists:roles,name'],
            'permissoes'   => ['nullable', 'array'],
            'permissoes.*' => ['exists:permissions,name'],
        ], [
            'name.required'     => 'O nome é obrigatório.',
            'email.required'    => 'O email é obrigatório.',
            'email.unique'      => 'Este email já está em uso.',
            'password.required' => 'A password é obrigatória.',
            'password.confirmed'=> 'As passwords não coincidem.',
            'role.required'     => 'Selecione um cargo.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles([$validated['role']]);

        if ($validated['role'] !== 'super-admin') {
            $user->syncPermissions($validated['permissoes'] ?? []);
        }

        return redirect()->route('admin.users.index')
            ->with('success', "Utilizador {$user->name} criado com sucesso.");
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role'         => ['required', 'exists:roles,name'],
            'permissoes'   => ['nullable', 'array'],
            'permissoes.*' => ['exists:permissions,name'],
        ];
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }
        $validated = $request->validate($rules, [
            'name.required'  => 'O nome é obrigatório.',
            'email.required' => 'O email é obrigatório.',
            'email.unique'   => 'Este email já está em uso.',
            'role.required'  => 'Selecione um cargo.',
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        $user->syncRoles([$validated['role']]);

        if ($validated['role'] !== 'super-admin') {
            $user->syncPermissions($validated['permissoes'] ?? []);
        } else {
            $user->syncPermissions([]);
        }

        return back()->with('success', "Utilizador {$user->name} actualizado com sucesso.");
    }

    public function toggleAdmin(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Não podes remover o teu próprio acesso de administrador!');
        }
        if ($user->hasRole('super-admin')) {
            $user->removeRole('super-admin');
            if (Role::where('name', 'utilizador')->exists()) {
                $user->assignRole('utilizador');
            }
            $msg = "Acesso de Admin removido para {$user->name}";
        } else {
            $user->assignRole('super-admin');
            $msg = "{$user->name} agora é Super Admin!";
        }
        return back()->with('success', $msg);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Não podes eliminar a tua própria conta!');
        }
        $user->delete();
        return back()->with('success', 'Utilizador eliminado com sucesso.');
    }
}