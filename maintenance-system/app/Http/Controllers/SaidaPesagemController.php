<?php

namespace App\Http\Controllers;

use App\Models\DischargeOperation;
use App\Models\SaidaCarga;
use App\Models\SaidaCargaAtrelado;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Pesagem dos camiões de uma saída.
 *  1ª pesagem (vazio): grava peso e atrelados, atribui o nº de ticket → "em_carga".
 *  2ª pesagem (cheio): grava peso, calcula o líquido, confirma produto e sacos
 *                      e só aqui desconta o stock → "carregada".
 * O ticket pode ser emitido depois de qualquer das duas pesagens.
 */
class SaidaPesagemController extends Controller
{
    /** Aviso se o peso líquido se afasta muito de sacos × peso do saco. */
    public static function avisoPeso(float $sacos, float $liquido): ?string
    {
        $peso = (float) config('saidas.peso_saco_kg');
        $tol  = (float) config('saidas.tolerancia_pct');

        $esperado = $sacos * $peso;
        if ($esperado <= 0 || $peso <= 0) {
            return null;
        }

        $dif = ($liquido - $esperado) / $esperado * 100;
        if (abs($dif) <= $tol) {
            return null;
        }

        return sprintf(
            'Líquido %s kg vs %s kg esperados (%s%.1f%%)',
            number_format($liquido, 0, ',', '.'),
            number_format($esperado, 0, ',', '.'),
            $dif > 0 ? '+' : '',
            $dif
        );
    }

    /* ───────────── 1ª pesagem (vazio) ───────────── */
    public function vazio(Request $request, SaidaCarga $camiao)
    {
        $saida = $camiao->saida;
        abort_if(! $saida, 404, 'Camião sem saída associada.');
        abort_if($saida->isFechada(), 403, 'Saída fechada.');
        abort_unless($camiao->status === 'pendente', 422, 'Este camião já tem a 1ª pesagem.');

        $dados = $request->validate([
            'peso_vazio'  => 'required|numeric|gt:0',
            'atrelados'   => 'nullable|array|max:8',
            'atrelados.*' => 'nullable|string|max:20',
        ]);

        $atrelados = collect($dados['atrelados'] ?? [])
            ->map(fn ($m) => strtoupper(trim((string) $m)))
            ->filter()->unique()->values();

        DB::transaction(function () use ($camiao, $dados, $atrelados) {
            $c = SaidaCarga::whereKey($camiao->id)->lockForUpdate()->firstOrFail();
            if ($c->status !== 'pendente') {
                throw ValidationException::withMessages(['peso_vazio' => 'Este camião já tem a 1ª pesagem.']);
            }

            $c->forceFill([
                'status'         => 'em_carga',
                'ticket_numero'  => $this->gerarTicket(),
                'peso_vazio'     => $dados['peso_vazio'],
                'peso_vazio_em'  => now(),
                'peso_vazio_por' => auth()->id(),
            ])->save();

            SaidaCargaAtrelado::where('saida_carga_id', $c->id)->delete();
            foreach ($atrelados as $m) {
                SaidaCargaAtrelado::create(['saida_carga_id' => $c->id, 'matricula' => $m]);
            }
        });

        return response()->json(['ok' => true, 'ticket' => route('saidas.camioes.ticket', $camiao)]);
    }

    /* ───────────── 2ª pesagem (cheio) ───────────── */
    public function cheio(Request $request, SaidaCarga $camiao)
    {
        $saida = $camiao->saida;
        abort_if(! $saida, 404, 'Camião sem saída associada.');
        abort_if($saida->isFechada(), 403, 'Saída fechada.');
        abort_unless($camiao->status === 'em_carga', 422, 'Faça primeiro a 1ª pesagem (vazio).');

        $dados = $request->validate([
            'peso_cheio'        => 'required|numeric|gt:0',
            'operation_item_id' => 'required|integer',
            'quantidade'        => 'required|numeric|gt:0',
            'observacoes'       => 'nullable|string|max:500',
        ]);

        if (! $saida->operation_id) {
            throw ValidationException::withMessages([
                'operation_item_id' => 'Esta saída não tem operação de origem definida.',
            ]);
        }

        $q = (float) $dados['quantidade'];

        DB::transaction(function () use ($camiao, $saida, $dados, $q) {
            // bloqueia o camião e a operação: sem duplo carregamento nem stock negativo
            $c = SaidaCarga::whereKey($camiao->id)->lockForUpdate()->firstOrFail();
            if ($c->status !== 'em_carga') {
                throw ValidationException::withMessages(['peso_cheio' => 'Este camião já não está em carga.']);
            }

            $cheio = (float) $dados['peso_cheio'];
            $vazio = (float) $c->peso_vazio;
            if ($cheio <= $vazio) {
                throw ValidationException::withMessages([
                    'peso_cheio' => 'O peso cheio tem de ser maior que o peso vazio.',
                ]);
            }

            $operation = DischargeOperation::with('items')->lockForUpdate()->findOrFail($saida->operation_id);
            abort_if($operation->isArchived(), 403, 'Operação arquivada.');

            $item = $operation->items->firstWhere('id', (int) $dados['operation_item_id']);
            if (! $item) {
                throw ValidationException::withMessages([
                    'operation_item_id' => 'Este produto não consta na operação.',
                ]);
            }

            if (($item->unidade ?? null) === 'saco' && floor($q) != $q) {
                throw ValidationException::withMessages(['quantidade' => 'Indique um número inteiro de sacos.']);
            }

            $disp = SaidaCarga::disponivelOperacao($operation, $item->id);
            if ($q > $disp + 0.0005) {
                throw ValidationException::withMessages([
                    'quantidade' => sprintf(
                        'Stock insuficiente nesta operação. Disponível: %s %s.',
                        rtrim(rtrim(number_format($disp, 3, ',', '.'), '0'), ','),
                        $item->unidade_label
                    ),
                ]);
            }

            $c->forceFill([
                'status'            => 'carregada',
                'operation_id'      => $operation->id,
                'discharge_id'      => null,
                'operation_item_id' => $item->id,
                'quantidade'        => $q,
                'quantidade_pedida' => $c->quantidade_pedida ?? $c->quantidade,
                'observacoes'       => $dados['observacoes'] ?? null,
                'peso_cheio'        => $cheio,
                'peso_cheio_em'     => now(),
                'peso_cheio_por'    => auth()->id(),
                'peso_liquido'      => round($cheio - $vazio, 3),
                'carregado_em'      => now(),
                'carregado_por'     => auth()->id(),
            ])->save();
        });

        return response()->json(['ok' => true, 'ticket' => route('saidas.camioes.ticket', $camiao)]);
    }

    /* ───────────── Anular a 1ª pesagem ───────────── */
    public function anular(SaidaCarga $camiao)
    {
        $saida = $camiao->saida;
        abort_if($saida && $saida->isFechada(), 403, 'Saída fechada.');
        abort_unless($camiao->status === 'em_carga', 422, 'Só se anula a 1ª pesagem de um camião em carga.');

        DB::transaction(function () use ($camiao) {
            SaidaCargaAtrelado::where('saida_carga_id', $camiao->id)->delete();

            $camiao->forceFill([
                'status'         => 'pendente',
                'ticket_numero'  => null,
                'peso_vazio'     => null,
                'peso_vazio_em'  => null,
                'peso_vazio_por' => null,
            ])->save();
        });

        return redirect()->route('saidas.show', $camiao->saida_id)
            ->with('success', '↩️ 1ª pesagem anulada. O camião voltou a "Por carregar".');
    }

    /* ───────────── Ticket (1ª ou 2ª pesagem) ───────────── */
    public function ticket(SaidaCarga $camiao)
    {
        abort_if($camiao->ticket_numero === null, 404, 'Este camião ainda não foi pesado.');

        $saida = $camiao->saida;
        abort_if(! $saida, 404, 'Camião sem saída associada.');

        $op   = $saida->operation()->with('items')->first();
        $item = ($camiao->operation_item_id && $op) ? $op->items->firstWhere('id', $camiao->operation_item_id) : null;

        $atrelados = SaidaCargaAtrelado::where('saida_carga_id', $camiao->id)->orderBy('id')->get();
        $users     = User::whereIn('id', array_filter([$camiao->peso_vazio_por, $camiao->peso_cheio_por]))->pluck('name', 'id');

        $vazioEm = $camiao->peso_vazio_em ? Carbon::parse($camiao->peso_vazio_em) : null;
        $cheioEm = $camiao->peso_cheio_em ? Carbon::parse($camiao->peso_cheio_em) : null;

        $aviso = null;
        if ($item && ($item->unidade ?? null) === 'saco' && $camiao->peso_liquido !== null) {
            $aviso = self::avisoPeso((float) $camiao->quantidade, (float) $camiao->peso_liquido);
        }

        return view('saidas.ticket', compact('camiao', 'saida', 'op', 'item', 'atrelados', 'users', 'vazioEm', 'cheioEm', 'aviso'));
    }

    /** TKT-2026-00001, sequencial por ano. */
    private function gerarTicket(): string
    {
        $ano    = now()->format('Y');
        $ultimo = SaidaCarga::where('ticket_numero', 'like', "TKT-{$ano}-%")->orderByDesc('ticket_numero')->value('ticket_numero');
        $n      = $ultimo ? ((int) substr($ultimo, -5)) + 1 : 1;

        return sprintf('TKT-%s-%05d', $ano, $n);
    }
}
