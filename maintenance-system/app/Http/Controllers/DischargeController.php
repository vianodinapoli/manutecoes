<?php

namespace App\Http\Controllers;

use App\Models\Discharge;
use App\Models\DischargeOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DischargeController extends Controller
{
    /** A listagem vive agora dentro de cada operação. */
    public function index()
    {
        return redirect()->route('operations.index');
    }

    public function create(Request $request)
    {
        if (! $request->ajax() && ! $request->has('modal')) {
            return redirect()->route('operations.index');
        }

        $operation = DischargeOperation::findOrFail($request->query('operation_id'));
        abort_unless($operation->isActive(), 403, 'Inicie a operação antes de lançar camiões.');

        $items = $this->itensComSaldo($operation, null);

        return view('discharges.create', compact('operation', 'items'));
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

        DB::transaction(function () use ($request, $operation, $items, $qtys) {
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
        });

        return redirect()->route('operations.show', $operation)
            ->with('success', '✅ Descarga registada com sucesso! Aguarda confirmação na balança.');
    }

    public function show(Request $request, Discharge $discharge)
    {
        if (! $request->ajax() && ! $request->has('modal')) {
            return redirect()->route('operations.show', $discharge->operation_id);
        }

        $discharge->load(['items.operationItem', 'operation', 'registeredBy', 'confirmedBy', 'signature']);

        return view('discharges.show', compact('discharge'));
    }

    public function edit(Request $request, Discharge $discharge)
    {
        if (! $request->ajax() && ! $request->has('modal')) {
            return redirect()->route('operations.show', $discharge->operation_id);
        }

        $operation = $discharge->operation;
        abort_unless($operation && ! $operation->isArchived(), 403, 'Operação arquivada.');

        $items   = $this->itensComSaldo($operation, $discharge);
        $actuais = $discharge->items()->pluck('quantidade', 'operation_item_id');

        return view('discharges.edit', compact('discharge', 'operation', 'items', 'actuais'));
    }

    public function update(Request $request, Discharge $discharge)
    {
        $operation = $discharge->operation;
        abort_unless($operation && ! $operation->isArchived(), 403, 'Operação arquivada.');

        $request->validate([
            'data'             => 'required|date',
            'motorista'        => 'required|string|max:100',
            'matricula'        => 'required|string|max:20',
            'transportadora'   => 'required|string|max:100',
            'numero_guia'      => 'required|string|unique:descargas,numero_guia,' . $discharge->id,
            'hora_saida_porto' => 'nullable|date_format:H:i',
            'tipo_atrelado'    => 'required|in:normal,link',
            'items'            => 'required|array',
            'items.*'          => 'nullable|numeric|min:0',
        ]);

        [$items, $qtys] = $this->quantidades($request, $operation, $discharge);

        DB::transaction(function () use ($request, $operation, $discharge, $items, $qtys) {
            $transp = $this->resolverTransportadora($operation, $request->transportadora);

            $discharge->update([
                'data'             => $request->data,
                'motorista'        => $request->motorista,
                'matricula'        => $request->matricula,
                'transportadora'   => $transp,
                'tipo_atrelado'    => $request->tipo_atrelado,
                'numero_guia'      => $request->numero_guia,
                'hora_saida_porto' => $request->hora_saida_porto,
            ] + $this->totais($items, $qtys));

            $this->gravarItens($discharge, $qtys);
        });

        return redirect()->route('operations.show', $operation)
            ->with('success', '✅ Descarga atualizada com sucesso!');
    }

    /**
     * Pesagem na balança, em duas fases:
     *   1. o camião chega cheio  → regista-se o peso cheio (bruto) e as quantidades
     *   2. o camião volta vazio  → regista-se o peso vazio (tara)
     * Os pesos podem ser registados em qualquer ordem e em submissões separadas.
     * Só quando existem os dois é que o estado passa a "confirmed".
     * Enquanto falta um, o estado é "weighing" (A pesar).
     */
    public function confirm(Request $request, Discharge $discharge)
    {
        $discharge->load('items.operationItem', 'operation');
        abort_if($discharge->operation && $discharge->operation->isArchived(), 403, 'Operação arquivada.');

        // as quantidades por produto só são obrigatórias na primeira passagem
        $jaTemQtd = $discharge->items->isNotEmpty()
            && $discharge->items->every(fn ($i) => $i->quantidade_confirmada !== null);

        $request->validate([
            'hora_chegada_balanca' => [$discharge->hora_chegada_balanca ? 'nullable' : 'required', 'date_format:H:i,H:i:s'],
            'peso_bruto'           => 'nullable|numeric|min:0',
            'tara'                 => 'nullable|numeric|min:0',
            'confirmados'          => [$jaTemQtd ? 'nullable' : 'required', 'array'],
            'confirmados.*'        => 'required|numeric|min:0',
            'observacoes'          => 'nullable|string|max:500',
        ]);

        // pesos finais = o que veio agora, ou o que já estava gravado
        $brutoActual = $discharge->peso_bruto !== null ? (float) $discharge->peso_bruto : null;
        $taraActual  = $discharge->tara !== null ? (float) $discharge->tara : null;

        $bruto = $request->filled('peso_bruto') ? (float) $request->peso_bruto : $brutoActual;
        $tara  = $request->filled('tara')       ? (float) $request->tara       : $taraActual;

        if ($bruto === null && $tara === null) {
            throw ValidationException::withMessages([
                'peso_bruto' => 'Registe pelo menos um peso (cheio ou vazio).',
            ]);
        }

        if ($bruto !== null && $tara !== null && $bruto <= $tara) {
            throw ValidationException::withMessages([
                'peso_bruto' => 'O peso cheio tem de ser maior que o peso vazio.',
            ]);
        }

        DB::transaction(function () use ($request, $discharge, $bruto, $tara, $brutoActual, $taraActual) {
            // Quantidades confirmadas por produto (primeira passagem, ou correcção)
            if ($request->filled('confirmados')) {
                $sacosConfirmados = 0;

                foreach ($discharge->items as $item) {
                    $q = (float) $request->input("confirmados.{$item->operation_item_id}", 0);

                    $item->update(['quantidade_confirmada' => $q]);

                    if ($item->operationItem->unidade === 'saco') {
                        $sacosConfirmados += (int) $q;
                    }
                }

                $discharge->sacos_confirmados = $sacosConfirmados;
            }

            if ($request->filled('hora_chegada_balanca')) {
                $discharge->hora_chegada_balanca = $request->hora_chegada_balanca;
            }

            // só carimba a hora da pesagem quando o peso é novo ou foi alterado
            if ($request->filled('peso_bruto') && $bruto !== $brutoActual) {
                $discharge->peso_bruto_em = now();
            }
            if ($request->filled('tara') && $tara !== $taraActual) {
                $discharge->tara_em = now();
            }

            $completo = $bruto !== null && $tara !== null;

            $discharge->peso_bruto     = $bruto;
            $discharge->tara           = $tara;
            $discharge->peso_liquido   = $completo ? $bruto - $tara : null;
            $discharge->observacoes    = $request->observacoes;
            $discharge->status         = $completo ? 'confirmed' : 'weighing';
            $discharge->confirmado_por = $completo ? auth()->id() : null;

            $discharge->calculateTransportTime();
            $discharge->save();
        });

        // recarrega para avaliar a divergência com o que ficou gravado
        $discharge->refresh()->load('items');
        $divergencia = $discharge->hasDivergence();

        $message = match (true) {
            $discharge->status === 'weighing' && $discharge->peso_bruto !== null
                => '⏳ Peso cheio registado. Falta pesar o camião vazio.' . ($divergencia ? ' ⚠️ Há divergência nas quantidades.' : ''),
            $discharge->status === 'weighing'
                => '⏳ Peso vazio registado. Falta pesar o camião cheio.' . ($divergencia ? ' ⚠️ Há divergência nas quantidades.' : ''),
            $divergencia
                => '⚠️ Confirmado com divergência entre o registado no porto e a balança!',
            default
                => '✅ Descarga confirmada com sucesso!',
        };

        return redirect()->route('operations.show', $discharge->operation_id)
            ->with('success', $message)
            ->with('ticket_url', route('discharges.ticket', $discharge));
    }

    /** Ticket de confirmação (imprimível), com a assinatura do motorista. */
    public function ticket(Discharge $discharge)
    {
        $discharge->load(['operation', 'items.operationItem', 'signature', 'registeredBy', 'confirmedBy']);

        return view('discharges.ticket', compact('discharge'));
    }

    public function destroy(Discharge $discharge)
    {
        $operation = $discharge->operation;
        abort_if($operation && $operation->isArchived(), 403, 'Operação arquivada.');

        $discharge->delete(); // discharge_items apagam em cascata

        return redirect()->route('operations.show', $discharge->operation_id)
            ->with('success', '🗑️ Registo eliminado.');
    }

    /* ───────────────────────── helpers ───────────────────────── */

    /**
     * Produtos do plano com o saldo por lançar.
     * $ignorar = camião a ignorar no cálculo (ao editar, para não contar duas vezes).
     */
    private function itensComSaldo(DischargeOperation $operation, ?Discharge $ignorar)
    {
        return $operation->items()
            ->withSum(['dischargeItems as registado' => function ($q) use ($ignorar) {
                if ($ignorar) {
                    $q->where('discharge_id', '!=', $ignorar->id);
                }
            }], 'quantidade')
            ->get();
    }

    /**
     * Lê items[<id do produto>] = quantidade, valida contra o plano e devolve
     * [produtos indexados por id, quantidades > 0 por id].
     */
    private function quantidades(Request $request, DischargeOperation $operation, ?Discharge $ignorar): array
    {
        $items = $this->itensComSaldo($operation, $ignorar)->keyBy('id');
        $input = $request->input('items', []);

        $qtys  = [];
        $erros = [];

        foreach ($items as $id => $it) {
            $q = (float) ($input[$id] ?? 0);
            if ($q <= 0) {
                continue;
            }

            if ($it->unidade === 'saco' && floor($q) != $q) {
                $erros["items.$id"] = "{$it->nome}: indique um número inteiro de sacos.";
                continue;
            }

            $saldo = (float) $it->quantidade - (float) ($it->registado ?? 0);
            if ($q > $saldo + 0.0005) {
                $erros["items.$id"] = sprintf(
                    '%s: ultrapassa o plano da operação. Saldo disponível: %s %s.',
                    $it->nome,
                    rtrim(rtrim(number_format($saldo, 3, ',', '.'), '0'), ','),
                    $it->unidade_label
                );
                continue;
            }

            $qtys[$id] = $q;
        }

        if (! $erros && ! $qtys) {
            $erros['items'] = 'Indique a quantidade de pelo menos um produto.';
        }

        if ($erros) {
            throw ValidationException::withMessages($erros);
        }

        return [$items, $qtys];
    }

    /**
     * Se a operação tem lista de transportadoras e esta não consta, acrescenta-a
     * marcada como "extra" (aparece a laranja). Devolve o nome canónico.
     * Sem lista definida, o campo é livre e nada é marcado.
     */
    private function resolverTransportadora(DischargeOperation $operation, string $nome): string
    {
        $nome  = trim(preg_replace('/\s+/', ' ', $nome));
        $lista = $operation->transportadoras ?? [];

        if (! $lista) {
            return $nome;
        }

        foreach ($lista as $t) {
            if (mb_strtolower($t['nome']) === mb_strtolower($nome)) {
                return $t['nome'];
            }
        }

        $lista[] = ['nome' => $nome, 'extra' => true];
        $operation->update(['transportadoras' => $lista]);

        return $nome;
    }

    /** Grava/actualiza as linhas do camião e remove as que ficaram a zero. */
    private function gravarItens(Discharge $discharge, array $qtys): void
    {
        $existentes = $discharge->items()->get()->keyBy('operation_item_id');

        foreach ($qtys as $id => $q) {
            if ($row = $existentes->get($id)) {
                $row->update(['quantidade' => $q]);
            } else {
                $discharge->items()->create(['operation_item_id' => $id, 'quantidade' => $q]);
            }
        }

        $discharge->items()->whereNotIn('operation_item_id', array_keys($qtys))->delete();
    }

    /**
     * Totais do camião. Mantém também as colunas antigas (sacos_alta, etc.)
     * para produtos chamados "Alta" / "Baixa", para não partir dashboards/API existentes.
     */
    private function totais($items, array $qtys): array
    {
        $t = [
            'total_sacos'         => 0,
            'peso_total_estimado' => 0,
            'sacos_alta'          => 0, 'peso_saco_alta'  => 0, 'total_alta'  => 0,
            'sacos_baixa'         => 0, 'peso_saco_baixa' => 0, 'total_baixa' => 0,
        ];

        foreach ($qtys as $id => $q) {
            $it = $items[$id];
            $kgPorUnidade = $it->unidade === 'tonelada' ? 1000 : (float) $it->peso_unitario_kg;
            $peso = $q * $kgPorUnidade;

            $t['peso_total_estimado'] += $peso;
            if ($it->unidade === 'saco') {
                $t['total_sacos'] += (int) $q;
            }

            $nome = mb_strtolower(trim($it->nome));
            if ($nome === 'alta') {
                $t['sacos_alta'] = (int) $q; $t['peso_saco_alta'] = $kgPorUnidade; $t['total_alta'] = $peso;
            } elseif ($nome === 'baixa') {
                $t['sacos_baixa'] = (int) $q; $t['peso_saco_baixa'] = $kgPorUnidade; $t['total_baixa'] = $peso;
            }
        }

        return $t;
    }
}