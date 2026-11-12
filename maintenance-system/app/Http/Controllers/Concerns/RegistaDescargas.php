<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Discharge;
use App\Models\DischargeOperation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait RegistaDescargas
{
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
