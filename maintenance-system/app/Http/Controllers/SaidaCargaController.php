<?php

namespace App\Http\Controllers;

use App\Models\DischargeOperation;
use App\Models\Saida;
use App\Models\SaidaCarga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Camiões de uma saída. Só um camião REGISTADO na saída (linha pendente)
 * pode carregar, e carrega uma só vez por lista: depois de carregar, a
 * matrícula não pode ser registada outra vez nesta saída.
 */
class SaidaCargaController extends Controller
{
    /** Regista um camião à mão (alternativa ao upload do Excel). */
    public function store(Request $request, Saida $saida)
    {
        abort_if($saida->isFechada(), 403, 'Saída fechada.');

        $request->validate([
            'matricula'         => 'required|string|max:20',
            'nome'              => 'required|string|max:100',
            'apelido'           => 'nullable|string|max:100',
            'empresa'           => 'nullable|string|max:120',
            'quantidade'        => 'required|numeric|gt:0',
            'tipo_atrelado'     => 'nullable|in:normal,link',
            'operation_item_id' => 'nullable|integer',
            'meta_k.*'          => 'nullable|string|max:60',
            'meta_v.*'          => 'nullable|string|max:200',
        ]);

        $matricula = strtoupper(trim($request->matricula));
        $norm      = fn ($m) => strtoupper(preg_replace('/\s+/', '', (string) $m));

        // um camião só consta uma vez por lista (pendente ou já carregado)
        $duplicado = $saida->camioes()->pluck('matricula')
            ->contains(fn ($m) => $norm($m) === $norm($matricula));
        if ($duplicado) {
            throw ValidationException::withMessages([
                'matricula' => "{$matricula} já consta nesta saída. Cada camião carrega uma vez por lista.",
            ]);
        }

        $itemId = null;
        if ($request->filled('operation_item_id')) {
            $item = $saida->operation?->items->firstWhere('id', (int) $request->operation_item_id);
            if (! $item) {
                throw ValidationException::withMessages([
                    'operation_item_id' => 'Este produto não consta na operação de origem.',
                ]);
            }
            $itemId = $item->id;
        }

        // metadados: pares chave/valor, ignorando linhas vazias
        $meta = [];
        foreach ($request->input('meta_k', []) as $i => $k) {
            $k = trim((string) $k);
            $v = trim((string) ($request->input('meta_v', [])[$i] ?? ''));
            if ($k !== '' && $v !== '') {
                $meta[$k] = $v;
            }
        }

        SaidaCarga::create([
            'saida_id'          => $saida->id,
            'data'              => $saida->data,
            'empresa'           => $request->filled('empresa') ? trim($request->empresa) : $saida->cliente,
            'nome'              => trim($request->nome),
            'apelido'           => $request->filled('apelido') ? trim($request->apelido) : null,
            'matricula'         => $matricula,
            'tipo_atrelado'     => $request->tipo_atrelado,
            'quantidade'        => $request->quantidade,
            'quantidade_pedida' => $request->quantidade,
            'operation_item_id' => $itemId,
            'metadata'          => $meta ?: null,
            'status'            => 'pendente',
            'registado_por'     => auth()->id(),
        ]);

        return redirect()->route('saidas.show', $saida)->with('success', "✅ Camião {$matricula} registado.");
    }

    /**
     * Carrega um camião registado: grava operação e produto e desconta a
     * quantidade do stock da operação de origem da saída.
     */
    public function carregar(Request $request, SaidaCarga $camiao)
    {
        $saida = $camiao->saida;
        abort_if(! $saida, 404, 'Camião sem saída associada.');
        abort_if($saida->isFechada(), 403, 'Saída fechada.');
        abort_unless($camiao->status === 'pendente', 422, 'Este camião já foi carregado nesta lista.');

        $dados = $request->validate([
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
            $camiao = SaidaCarga::whereKey($camiao->id)->lockForUpdate()->firstOrFail();
            if ($camiao->status !== 'pendente') {
                throw ValidationException::withMessages(['quantidade' => 'Este camião já foi carregado nesta lista.']);
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

            $camiao->update([
                'status'            => 'carregada',
                'operation_id'      => $operation->id,
                'discharge_id'      => null,
                'operation_item_id' => $item->id,
                'quantidade'        => $q,
                'quantidade_pedida' => $camiao->quantidade_pedida ?? $camiao->quantidade,
                'observacoes'       => $dados['observacoes'] ?? null,
                'carregado_em'      => now(),
                'carregado_por'     => auth()->id(),
            ]);
        });

        return response()->json(['ok' => true]);
    }

    /** Desfaz a 2ª pesagem: repõe o stock e o camião volta a "em carga" (ou a "por carregar" se não tiver peso vazio). */
public function devolver(SaidaCarga $camiao)
{
    $saida = $camiao->saida;
    abort_if($saida && $saida->isFechada(), 403, 'Saída fechada. Reabra-a para devolver.');
    abort_unless($camiao->status === 'carregada', 422, 'Este camião não está carregado.');

    $operation = $camiao->operation;
    abort_if($operation && $operation->isArchived(), 403, 'Operação arquivada.');

    $camiao->forceFill([
        'status'         => $camiao->peso_vazio !== null ? 'em_carga' : 'pendente',
        'operation_id'   => null,
        'discharge_id'   => null,
        'quantidade'     => $camiao->quantidade_pedida ?? $camiao->quantidade,
        'peso_cheio'     => null,
        'peso_cheio_em'  => null,
        'peso_cheio_por' => null,
        'peso_liquido'   => null,
        'carregado_em'   => null,
        'carregado_por'  => null,
    ])->save();

    return redirect()->route('saidas.show', $camiao->saida_id)
        ->with('success', '↩️ Carregamento desfeito. Stock reposto.');
}

    /** Remove um camião registado que ainda não carregou. */
    public function destroy(SaidaCarga $camiao)
    {
        $saida = $camiao->saida;
        abort_if($saida && $saida->isFechada(), 403, 'Saída fechada.');
        abort_unless($camiao->status === 'pendente', 422, 'Só se podem remover camiões que ainda não carregaram. Use "Devolver" primeiro.');

        $saidaId = $camiao->saida_id;
        $camiao->delete();

        return redirect()->route('saidas.show', $saidaId)->with('success', '🗑️ Camião removido da lista.');
    }
}