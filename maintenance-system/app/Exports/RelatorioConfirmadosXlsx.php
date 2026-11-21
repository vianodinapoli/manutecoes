<?php

namespace App\Exports;

use Carbon\Carbon;

final class RelatorioConfirmadosXlsx
{
    /** $r = o array que o endpoint já devolve; $ctx = navio, empresa, de, ate, confirmados. */
    public static function gerar(array $r, array $ctx)
    {
        $x = XlsxHelper::nova();
        $fmt = fn ($v) => Carbon::parse($v)->format('d/m/Y H:i');   // já vem em hora de Maputo

        // ── Folha 1: o balanço ──
        $s = XlsxHelper::folha($x, 'Balanço', true);
        XlsxHelper::titulo($s, 'Balanço de confirmados — ' . $ctx['navio'], (string) ($ctx['empresa'] ?? ''), 2);
        $l = 4;
        $l = XlsxHelper::par($s, $l, 'Período', $fmt($ctx['de']) . '  →  ' . $fmt($ctx['ate']));
        $l = XlsxHelper::par($s, $l, 'Gerado em', now(XlsxHelper::TZ)->format('d/m/Y H:i'));

        $l++;
        $l = XlsxHelper::secao($s, $l, 'Resumo (camiões)');
        $l = XlsxHelper::par($s, $l, 'Carregados', (int) $r['carregados'], XlsxHelper::INT);
        $l = XlsxHelper::par($s, $l, 'Em trânsito', (int) $r['em_transito'], XlsxHelper::INT);
        $l = XlsxHelper::par($s, $l, 'A pesar', (int) $r['a_pesar'], XlsxHelper::INT);
        $l = XlsxHelper::par($s, $l, 'Confirmados', (int) $r['camioes'], XlsxHelper::INT);

        $l++;
        $l = XlsxHelper::secao($s, $l, 'Carga (t)');
        $l = XlsxHelper::par($s, $l, 'Esperada', $r['esperada_ton'] !== null ? (float) $r['esperada_ton'] : null, XlsxHelper::T1);
        $l = XlsxHelper::par($s, $l, 'Confirmada', (float) $r['toneladas'], XlsxHelper::T1);
        $l = XlsxHelper::par($s, $l, 'Em trânsito', (float) $r['em_transito_ton'], XlsxHelper::T1);

        if (! empty($r['produtos'])) {
            $l++;
            $l = XlsxHelper::secao($s, $l, count($r['produtos']) > 1 ? 'Tipos de carga' : 'Tipo de carga');
            foreach ($r['produtos'] as $p) {
                $l = XlsxHelper::par($s, $l, $p['nome'] . ' (' . $p['unidade_label'] . ')', (float) $p['quantidade'], '#,##0.###');
            }
        }

        $l++;
        $l = XlsxHelper::secao($s, $l, 'Alertas');
        $l = XlsxHelper::par($s, $l, 'Camiões atrasados', (int) $r['atrasados'], XlsxHelper::INT, $r['atrasados'] > 0 ? 'B3261E' : '198754');
        XlsxHelper::par($s, $l, 'Camiões com divergência', (int) $r['divergencias'], XlsxHelper::INT, $r['divergencias'] > 0 ? 'B3261E' : '198754');
        XlsxHelper::larguras($s, [38, 32]);

        // ── Folha 2: camiões confirmados no intervalo ──
        $s = XlsxHelper::folha($x, 'Camiões confirmados');
        $cab = ['Nº', 'Data', 'Guia', 'Motorista', 'Matrícula', 'Transportadora', 'Confirmado em',
                'Carga (porto → balança)', 'Sacos', 'Bruto (kg)', 'Tara (kg)', 'Líquido (kg)', 'Divergência'];
        XlsxHelper::titulo($s, 'Camiões confirmados no período', $fmt($ctx['de']) . '  →  ' . $fmt($ctx['ate']), count($cab));
        XlsxHelper::cabecalho($s, 4, $cab);

        $qtd = fn ($v) => rtrim(rtrim(number_format((float) $v, 3, ',', '.'), '0'), ',');
        $r2 = 5;
        foreach ($ctx['confirmados']->sortBy('id') as $d) {
            $quando = collect([$d->peso_bruto_em, $d->tara_em])->filter()->max();
            $carga = $d->items->map(function ($l) use ($qtd) {
                $t = ($l->operationItem->nome ?? '—') . ': ' . $qtd($l->quantidade);
                return $l->quantidade_confirmada !== null ? $t . ' → ' . $qtd($l->quantidade_confirmada) : $t;
            })->implode(' | ');

            XlsxHelper::escrever($s, $r2, [
                (int) $d->id, XlsxHelper::dia($d->data), $d->numero_guia, $d->motorista, $d->matricula, $d->transportadora,
                XlsxHelper::dataHora($quando), $carga,
                (int) ($d->sacos_confirmados ?? $d->total_sacos),
                $d->peso_bruto !== null ? (float) $d->peso_bruto : null,
                $d->tara !== null ? (float) $d->tara : null,
                $d->peso_liquido !== null ? (float) $d->peso_liquido : null,
                $d->hasDivergence() ? 'Sim' : null,
            ]);
            $r2++;
        }
        $fim = $r2 - 1;
        XlsxHelper::formato($s, 5, $fim, [
            1 => '0', 2 => XlsxHelper::DATA, 7 => XlsxHelper::DATA_HORA,
            9 => XlsxHelper::INT, 10 => XlsxHelper::INT, 11 => XlsxHelper::INT, 12 => XlsxHelper::INT,
        ]);
        XlsxHelper::grelha($s, 5, $fim, count($cab));

        XlsxHelper::celula($s, 1, $r2, 'TOTAL');
        foreach ([9, 10, 11, 12] as $c) {
            XlsxHelper::soma($s, $r2, $c, 5, $fim, XlsxHelper::INT);
        }
        XlsxHelper::totais($s, $r2, count($cab));
        XlsxHelper::tabela($s, 4, $fim, count($cab));
        XlsxHelper::larguras($s, [7, 12, 14, 22, 14, 24, 17, 36, 9, 12, 12, 13, 12]);

        return XlsxHelper::descarregar($x, XlsxHelper::nome('Balanco', $ctx['navio']));
    }
}
