<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DischargeOperation;
use Illuminate\Http\Request;

class DischargeListApiController extends Controller
{
    public function index(Request $request, DischargeOperation $operation)
    {
        $q = $operation->discharges();

        if ($s = trim((string) $request->query('q'))) {
            $like = '%' . mb_strtolower($s) . '%';
            $q->where(function ($w) use ($like) {
                $w->whereRaw('LOWER(matricula) LIKE ?', [$like])
                  ->orWhereRaw('LOWER(motorista) LIKE ?', [$like])
                  ->orWhereRaw('LOWER(numero_guia) LIKE ?', [$like])
                  ->orWhereRaw('LOWER(transportadora) LIKE ?', [$like]);
            });
        }

        $st = (string) $request->query('st');
        if ($st === 'por_confirmar') {
            $q->whereIn('status', ['pending', 'in_transit']);
        } elseif (in_array($st, ['pending', 'in_transit', 'weighing', 'confirmed'], true)) {
            $q->where('status', $st);
        }

        if ($tr = trim((string) $request->query('tr'))) {
            $q->whereRaw('LOWER(transportadora) = ?', [mb_strtolower($tr)]);
        }

        $at = (string) $request->query('at');
        if ($at === 'nd') {
            $q->whereNull('tipo_atrelado');
        } elseif (in_array($at, ['normal', 'link'], true)) {
            $q->where('tipo_atrelado', $at);
        }

        if ($de = $request->query('de'))   { $q->whereDate('data', '>=', $de); }
        if ($ate = $request->query('ate')) { $q->whereDate('data', '<=', $ate); }

        $porPagina = in_array((int) $request->query('pp'), [15, 30, 50], true) ? (int) $request->query('pp') : 15;

        $p = $q->orderByDesc('id')->paginate($porPagina);

        $data = $p->getCollection()->map(fn ($d) => [
            'id'                  => $d->id,
            'data'                => $d->data->format('Y-m-d'),
            'hora_saida_porto'    => $d->hora_saida_porto ? substr((string) $d->hora_saida_porto, 0, 5) : null,
            'motorista'           => $d->motorista,
            'matricula'           => $d->matricula,
            'transportadora'      => $d->transportadora,
            'numero_guia'         => $d->numero_guia,
            'tipo_atrelado'       => $d->tipo_atrelado,
            'status'              => $d->status,
            'total_sacos'         => (int) $d->total_sacos,
            'peso_total_estimado' => (float) $d->peso_total_estimado,
        ])->values();

        $porEstado = $operation->discharges()
            ->selectRaw('status, COUNT(*) as n')
            ->groupBy('status')
            ->pluck('n', 'status');

        return [
            'data' => $data,
            'meta' => [
                'pagina'    => $p->currentPage(),
                'ultima'    => $p->lastPage(),
                'por_pagina'=> $p->perPage(),
                'total'     => $p->total(),
                'de'        => $p->firstItem(),
                'ate'       => $p->lastItem(),
            ],
            'contagens' => [
                'todos'         => (int) $porEstado->sum(),
                'por_confirmar' => (int) (($porEstado['pending'] ?? 0) + ($porEstado['in_transit'] ?? 0)),
                'weighing'      => (int) ($porEstado['weighing'] ?? 0),
                'confirmed'     => (int) ($porEstado['confirmed'] ?? 0),
            ],
            'transportadoras' => $operation->discharges()
                ->select('transportadora')->distinct()->orderBy('transportadora')
                ->pluck('transportadora')->values(),
        ];
    }
}
