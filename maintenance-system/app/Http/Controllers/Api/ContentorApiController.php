<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contentor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContentorApiController extends Controller
{
    /** Dias no parque a partir dos quais o contentor é considerado atrasado. */
    private const LIMITE_DIAS = 7;

    public function index(Request $request): JsonResponse
    {
        // mesma permissão que a dashboard web de contentores
        abort_unless($request->user()?->can('acesso discharges'), 403);

        $todos  = Contentor::orderByDesc('id')->get();
        $limite = self::LIMITE_DIAS;

        // Fisicamente no recinto = tudo menos "em trânsito" e "saiu"
        $dentro = $todos->reject(fn ($c) => in_array($c->estado, ['em_transito', 'saiu']));

        $kpis = [
            'total'       => $todos->count(),
            'no_parque'   => $dentro->count(),
            'em_transito' => $todos->where('estado', 'em_transito')->count(),
            'carregados'  => $todos->where('estado', 'carregado')->count(),
            'saiu'        => $todos->where('estado', 'saiu')->count(),
            'atrasados'   => $dentro->filter(fn ($c) => ($c->dias_no_parque ?? 0) > $limite)->count(),
            'peso_t'      => round($dentro->sum(fn ($c) => (float) $c->peso_kg) / 1000, 3),
            'limite_dias' => $limite,
        ];

        // Entradas por mês (últimos 12 meses)
        $inicio = now()->startOfMonth()->subMonths(11);
        $meses = [];
        $entradasMensais = [];
        for ($i = 0; $i < 12; $i++) {
            $m = $inicio->copy()->addMonths($i);
            $meses[] = $m->format('m/Y');
            $entradasMensais[] = $todos->filter(
                fn ($c) => $c->data_entrada && $c->data_entrada->format('Y-m') === $m->format('Y-m')
            )->count();
        }

        // Por estado
        $porEstado = collect(Contentor::ESTADOS)
            ->map(fn ($label, $k) => ['nome' => $label, 'total' => $todos->where('estado', $k)->count()])
            ->filter(fn ($e) => $e['total'] > 0)
            ->values();

        // Clientes com mais contentores no parque
        $topClientes = $dentro->filter(fn ($c) => filled($c->cliente))
            ->groupBy('cliente')
            ->map->count()
            ->sortDesc()
            ->take(8)
            ->map(fn ($total, $cliente) => ['cliente' => $cliente, 'total' => $total])
            ->values();

        $lista = $todos->map(function ($c) use ($limite) {
            $dias  = $c->dias_no_parque;
            $ativo = ! in_array($c->estado, ['em_transito', 'saiu']);

            return [
                'id'             => $c->id,
                'numero'         => $c->numero,
                'tipo'           => $c->tipo,
                'estado'         => $c->estado,
                'estado_label'   => Contentor::ESTADOS[$c->estado] ?? $c->estado,
                'cliente'        => $c->cliente,
                'navio'          => $c->navio,
                'bl'             => $c->bl,
                'selo'           => $c->selo,
                'peso_kg'        => $c->peso_kg !== null ? (float) $c->peso_kg : null,
                'localizacao'    => $c->localizacao,
                'data_entrada'   => $c->data_entrada?->format('Y-m-d'),
                'dias_no_parque' => $dias,
                'no_parque'      => $ativo,
                'atrasado'       => $ativo && $dias !== null && $dias > $limite,
            ];
        })->values();

        return response()->json([
            'kpis'             => $kpis,
            'meses'            => $meses,
            'entradas_mensais' => $entradasMensais,
            'por_estado'       => $porEstado,
            'top_clientes'     => $topClientes,
            'contentores'      => $lista,
        ]);
    }
}