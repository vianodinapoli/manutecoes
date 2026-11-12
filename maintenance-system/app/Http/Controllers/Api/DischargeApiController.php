<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\RegistaDescargas;
use App\Http\Controllers\Controller;
use App\Models\Discharge;
use App\Models\DischargeOperation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DischargeApiController extends Controller
{
    use RegistaDescargas;

    public function operations()
    {
        return DischargeOperation::open()
            ->where('status', 'active')
            ->latest()->get()
            ->map(fn ($op) => $this->operationPayload($op))
            ->values();
    }

    public function operation(DischargeOperation $operation)
    {
        return $this->operationPayload($operation);
    }

    public function store(Request $request)
    {
        $request->validate([
            'operation_id'     => 'required|exists:discharge_operations,id',
            'data'             => 'required|date',
            'motorista'        => 'required|string|max:100',
            'matricula'        => 'required|string|max:20',
            'transportadora'   => 'required|string|max:100',
            'numero_guia'      => 'required|string|unique:descargas,numero_guia',
            'hora_saida_porto' => 'nullable|date_format:H:i',
            'tipo_atrelado'    => 'required|in:normal,link',
            'items'            => 'required|array',
            'items.*'          => 'nullable|numeric|min:0',
            'assinatura'       => ['required', 'string', 'max:700000', function ($attr, $value, $fail) {
                $prefixo = 'data:image/png;base64,';
                $bin = str_starts_with($value, $prefixo) ? base64_decode(substr($value, strlen($prefixo)), true) : false;
                if ($bin === false || ! str_starts_with($bin, "\x89PNG")) {
                    $fail('A assinatura do motorista é inválida. Peça-lhe para assinar de novo.');
                }
            }],
        ], [
            'assinatura.required' => 'A assinatura do motorista é obrigatória.',
        ]);

        $operation = DischargeOperation::findOrFail($request->operation_id);
        abort_unless($operation->isActive(), 403, 'A operação não está em curso.');

        [$items, $qtys] = $this->quantidades($request, $operation, null);

        $discharge = DB::transaction(function () use ($request, $operation, $items, $qtys) {
            $transp = $this->resolverTransportadora($operation, $request->transportadora);

            $discharge = Discharge::create([
                'operation_id'     => $operation->id,
                'data'             => $request->data,
                'motorista'        => $request->motorista,
                'matricula'        => $request->matricula,
                'transportadora'   => $transp,
                'tipo_atrelado'    => $request->tipo_atrelado,
                'numero_guia'      => $request->numero_guia,
                'hora_saida_porto' => $request->hora_saida_porto,
                'registado_por'    => auth()->id(),
                'status'           => 'in_transit',
            ] + $this->totais($items, $qtys));

            $this->gravarItens($discharge, $qtys);

            $discharge->signature()->create([
                'nome'         => $request->motorista,
                'imagem'       => $request->assinatura,
                'assinado_por' => auth()->id(),
            ]);

            return $discharge;
        });

        return response()->json([
            'id'      => $discharge->id,
            'message' => 'Descarga registada. Aguarda confirmação na balança.',
        ], 201);
    }

    private function operationPayload(DischargeOperation $op): array
    {
        $camioes   = $op->discharges()->count();
        $pendentes = $op->discharges()->whereIn('status', ['pending', 'in_transit'])->count();
        $recebido  = (float) $op->discharges()->sum('peso_liquido');

        $ts = $op->discharges()
            ->whereIn('status', ['pending', 'in_transit'])
            ->whereNotNull('hora_saida_porto')
            ->get()
            ->map(fn ($d) => Carbon::parse($d->data->format('Y-m-d') . ' ' . substr((string) $d->hora_saida_porto, 0, 5))->timestamp)
            ->min();

        return [
            'id'              => $op->id,
            'navio'           => $op->navio,
            'empresa'         => $op->empresa,
            'bl'              => $op->bl,
            'factura'         => $op->factura,
            'data_chegada'    => $op->data_chegada ? $op->data_chegada->format('d/m/Y') : null,
            'status'          => $op->status,
            'esperado_ton'    => (float) $op->quantidade_esperada,
            'recebido_ton'    => round($recebido / 1000, 3),
            'camioes_count'   => $camioes,
            'pendentes_count' => $pendentes,
            'pendente_desde'  => $ts ? $ts * 1000 : null,
            'transportadoras' => collect($op->transportadoras ?? [])->pluck('nome')->values(),
            'items'           => $this->itensComSaldo($op, null)->map(fn ($it) => [
                'id'               => $it->id,
                'nome'             => $it->nome,
                'unidade'          => $it->unidade,
                'unidade_label'    => $it->unidade_label,
                'peso_unitario_kg' => (float) $it->peso_unitario_kg,
                'plano'            => (float) $it->quantidade,
                'registado'        => (float) ($it->registado ?? 0),
                'saldo'            => (float) $it->quantidade - (float) ($it->registado ?? 0),
            ])->values(),
        ];
    }
}
