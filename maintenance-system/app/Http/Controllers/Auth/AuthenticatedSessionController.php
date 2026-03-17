<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Redireciona para a primeira rota a que o utilizador tem acesso
        $destino = $this->resolverDestino($user);

        return redirect()->intended($destino);
    }

    /**
     * Determina para onde redirecionar o utilizador após login
     * com base nas suas permissões.
     */
    private function resolverDestino($user): string
    {
        // Super admin vai sempre para o dashboard
        if ($user->hasRole('super-admin')) {
            return route('dashboard');
        }

        // Ordem de prioridade — vai para a primeira que tiver acesso
        $mapa = [
            'acesso dashboard'    => 'dashboard',
            'acesso equipamentos' => 'machines.index',
            'acesso manutencoes'  => 'maintenances.index',
            'acesso stock'        => 'stock-items.index',
            'acesso movimentos'   => 'movimentos.index',
            'acesso pedidos'      => 'requisicoes.index',
            'acesso combustivel'  => 'fuel.index',
            'acesso discharges'   => 'discharges.index',
        ];

        foreach ($mapa as $permissao => $rota) {
            if ($user->can($permissao)) {
                return route($rota);
            }
        }

        // Fallback — perfil do utilizador (sempre acessível)
        return route('profile.edit');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}