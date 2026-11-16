<?php

namespace App\Support;

class HomeRedirect
{
    /** Ordem de prioridade: permissão => nome da rota */
    private const MAPA = [
        'acesso dashboard'      => 'dashboard',
        'acesso discharges'     => 'discharges.index',
        'acesso equipamentos'   => 'machines.index',
        'acesso manutencoes'    => 'maintenances.index',
        'acesso stock'          => 'stock-items.index',
        'acesso movimentos'     => 'movimentos.index',
        'acesso viaturas'       => 'viaturas.index',
        'acesso pedidos'        => 'requisicoes.index',
        'acesso combustivel'    => 'fuel.index',
        'acesso caixa'          => 'caixa.index',
        'requisicoes-material'  => 'requisicoes-material.index',
    ];

    public static function para($user): string
    {
        if ($user->hasRole('super-admin')) {
            return route('dashboard');
        }

        foreach (self::MAPA as $permissao => $rota) {
            if ($user->can($permissao)) {
                return route($rota);
            }
        }

        return route('profile.edit');
    }
}
