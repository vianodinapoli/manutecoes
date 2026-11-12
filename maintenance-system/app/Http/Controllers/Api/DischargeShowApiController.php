<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discharge;

class DischargeShowApiController extends Controller
{
    public function show(Discharge $discharge)
    {
        $discharge->load(['operation', 'items.operationItem', 'registeredBy', 'confirmedBy', 'signature']);

        $op     = $discharge->operation;
        $extras = $op ? $op->transportadorasExtra() : [];
        $sig    = $discharge->signature;

        $itens = $discharge->items->map(function ($l) {
            $it = $l->operationItem;
            $kg = $it->unidade === 'tonelada' ? 1000 : (float) $it->peso_unitario_kg;

            return [
                'id'                => $l->id,
                'nome'              => $it->nome,
                'unidade'           => $it->unidade,
                'unidade_label'     => $it->unidade_label,
                'quantidade'        => (float) $l->quantidade,
                'kg_unidade'        => $kg,
                'peso_kg'           => (float) $l->quantidade * $kg,
                'quantidade_confirmada' => $l->quantidade_confirmada !== null ? (float) $l->quantidade_confirmada : null,
            ];
        })->values();

        $balanca = null;
        if ($discharge->peso_bruto !== null || $discharge->tara !== null) {
            $balanca = [
                'hora_chegada' => $discharge->hora_chegada_balanca ? substr((string) $discharge->hora_chegada_balanca, 0, 5) : null,
                'peso_bruto'   => $discharge->peso_bruto !== null ? (float) $discharge->peso_bruto : null,
                'tara'         => $discharge->tara !== null ? (float) $discharge->tara : null,
                'peso_liquido' => $discharge->peso_liquido !== null ? (float) $discharge->peso_liquido : null,
                'confirmado_por' => $discharge->confirmedBy?->name,
                'divergencia'  => $discharge->hasDivergence(),
            ];
        }

        return [
            'id'                  => $discharge->id,
            'operation_id'        => $discharge->operation_id,
            'navio'               => $op?->navio,
            'status'              => $discharge->status,
            'data'                => $discharge->data->format('d/m/Y'),
            'numero_guia'         => $discharge->numero_guia,
            'hora_saida_porto'    => $discharge->hora_saida_porto ? substr((string) $discharge->hora_saida_porto, 0, 5) : null,
            'registado_por'       => $discharge->registeredBy?->name,
            'motorista'           => $discharge->motorista,
            'matricula'           => $discharge->matricula,
            'transportadora'      => $discharge->transportadora,
            'transportadora_extra'=> in_array(mb_strtolower((string) $discharge->transportadora), $extras, true),
            'tipo_atrelado'       => $discharge->tipo_atrelado,
            'atrelado_aviso'      => $discharge->atreladoInconsistente()
                                        ? 'não bate com os ' . (int) $discharge->total_sacos . ' sacos'
                                        : null,
            'assinatura'          => $sig ? [
                'imagem' => $sig->imagem,
                'nome'   => $sig->nome,
                'em'     => $sig->created_at?->format('d/m/Y H:i'),
            ] : null,
            'items'               => $itens,
            'total_sacos'         => (int) $discharge->total_sacos,
            'peso_total_estimado' => (float) $discharge->peso_total_estimado,
            'balanca'             => $balanca,
        ];
    }
}
