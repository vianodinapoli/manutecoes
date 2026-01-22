<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        // Ordenar por nome para facilitar a gestão
        $users = User::with('roles')->orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    public function toggleAdmin(User $user)
    {
        // SEGURANÇA: Impede que tires o teu próprio acesso de Admin
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Não podes remover o teu próprio acesso de administrador!');
        }

        if ($user->hasRole('super-admin')) {
            $user->removeRole('super-admin');
            
            // Garante que o papel 'utilizador' existe antes de atribuir
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
        // SEGURANÇA: Impede que apagues a ti próprio
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Não podes eliminar a tua própria conta!');
        }

        $user->delete();
        return back()->with('success', 'Utilizador eliminado com sucesso.');
    }
}