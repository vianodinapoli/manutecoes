<?php

namespace App\Exports;

use App\Models\Discharge;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

final class RelatorioOperacaoXlsx
{
    public static function gerar(array $d)
    {
        $op = $d['operation'];
        $x = XlsxHelper::nova();

        self::resumo($x, $op, $d['resumo'], $d['utilizador'] ?? '—');
        self::produtos($x, $d['porProduto']);
        self::transportadoras($x, $d['porTransp'], $d['semCamioes']);
        self::camioes($x, $d['discharges']);
        self::atrasos($x, $d['discharges']);
        self::historico($x, $d['historico']);

        return XlsxHelper::descarregar($x, XlsxHelper::nome('Relatorio', $op->navio));
    }

    private static function resumo(Spreadsheet $x, $op, array $r, string $utilizador): void
    {
        $s = XlsxHelper::folha($x, 'Resumo', true);
        $sub = trim($op->empresa . ($op->bl ? ' · BL ' . $op->bl : '') . ($op->factura ? ' · Factura ' . $op->factura : ''));
        XlsxHelper::titulo($s, 'Relatório de Descarga — ' . $op->navio, $sub, 2);

        $estado = match ($op->status) {
            'active' => 'Em curso',
            'archived' => 'Arquivada',
            default => $op->status_label ?? $op->status,
        };
        $hora = fn ($c) => $c ? Carbon::parse($c)->timezone(XlsxHelper::TZ)->format('d/m/Y H:i') : null;

        $l = 4;
        $l = XlsxHelper::secao($s, $l, 'Identificação');
        foreach ([
            ['Navio', $op->navio], ['Empresa', $op->empresa], ['Estado', $estado],
            ['BL', $op->bl], ['Factura', $op->factura],
            ['Data de chegada', $op->data_chegada ? Carbon::parse($op->data_chegada)->format('d/m/Y') : null],
            ['Início da descarga', $hora($op->started_at)],
            ['Arquivada em', $hora($op->archived_at)],
            ['Emitido em', now(XlsxHelper::TZ)->format('d/m/Y H:i')],
            ['Emitido por', $utilizador],
        ] as [$rot, $val]) {
            $l = XlsxHelper::par($s, $l, $rot, $val);
        }

        $l++;
        $l = XlsxHelper::secao($s, $l, 'Resumo executivo');
        $falta = (float) $r['falta_ton'];
        $l = XlsxHelper::par($s, $l, 'Esperado (t)', (float) $r['esperado_ton'], XlsxHelper::T1);
        $l = XlsxHelper::par($s, $l, 'Lançado no porto (t)', (float) $r['porto_ton'], XlsxHelper::T1);
        $l = XlsxHelper::par($s, $l, 'Lançado no porto (% do plano)', (float) $r['lancado_pct'] / 100, '0.0%');
        $l = XlsxHelper::par($s, $l, 'Pesado na balança (t)', (float) $r['balanca_ton'], XlsxHelper::T1);
        $l = XlsxHelper::par($s, $l, 'Pesado na balança (% do plano)', (float) $r['progresso'] / 100, '0.0%');
        $l = XlsxHelper::par($s, $l, $falta >= 0 ? 'Em falta (t)' : 'Excesso (t)', abs($falta), XlsxHelper::T1, $falta > 0 ? 'B3261E' : '198754');
        $l = XlsxHelper::par($s, $l, 'Camiões', (int) $r['camioes'], XlsxHelper::INT);
        $l = XlsxHelper::par($s, $l, 'Confirmados', (int) $r['confirmados'], XlsxHelper::INT);
        $l = XlsxHelper::par($s, $l, 'Por confirmar', (int) $r['por_confirmar'], XlsxHelper::INT);
        $l = XlsxHelper::par($s, $l, 'Divergências', (int) $r['divergencias'], XlsxHelper::INT, $r['divergencias'] > 0 ? 'C2410C' : '198754');
        $l = XlsxHelper::par($s, $l, 'Tempo médio porto → balança (min)', $r['tempo_medio'] !== null ? (int) $r['tempo_medio'] : null, XlsxHelper::INT);
        XlsxHelper::par($s, $l, 'Diferença pesado vs. estimado (kg)', (float) $r['dif_peso_kg'], XlsxHelper::DIF);

        XlsxHelper::larguras($s, [38, 30]);
    }

    private static function produtos(Spreadsheet $x, $porProduto): void
    {
        $s = XlsxHelper::folha($x, 'Produtos');
        $cab = ['Produto', 'Unidade', 'Peso por saco (kg)', 'Plano', 'No porto', 'Confirmado', 'Falta lançar',
                'Dif. balança vs. porto', 'Plano (t)', 'Porto (t)', 'Balança (t)'];
        XlsxHelper::titulo($s, 'Plano vs. realizado, por produto', 'Quantidades na unidade de cada produto; toneladas nas três últimas colunas.', count($cab));
        XlsxHelper::cabecalho($s, 4, $cab);

        $r = 5;
        foreach ($porProduto as $p) {
            $it = $p['item'];
            $saco = $it->unidade === 'saco';
            XlsxHelper::escrever($s, $r, [
                $it->nome, $it->unidade_label, $saco ? (float) $it->peso_unitario_kg : null,
                (float) $p['plano'], (float) $p['registado'], (float) $p['confirmado'],
                (float) $p['falta'], (float) $p['dif'],
                $p['plano_kg'] / 1000, $p['porto_kg'] / 1000, $p['confirmado_kg'] / 1000,
            ]);
            $q = $saco ? XlsxHelper::INT : XlsxHelper::T3;
            XlsxHelper::formato($s, $r, $r, [3 => XlsxHelper::T3, 4 => $q, 5 => $q, 6 => $q, 7 => $q, 8 => $q]);
            $r++;
        }
        $fim = $r - 1;
        XlsxHelper::formato($s, 5, $fim, [9 => XlsxHelper::T3, 10 => XlsxHelper::T3, 11 => XlsxHelper::T3]);
        XlsxHelper::grelha($s, 5, $fim, count($cab));

        XlsxHelper::celula($s, 1, $r, 'TOTAL');
        foreach ([9, 10, 11] as $c) {
            XlsxHelper::soma($s, $r, $c, 5, $fim, XlsxHelper::T3);
        }
        XlsxHelper::totais($s, $r, count($cab));
        XlsxHelper::tabela($s, 4, $fim, count($cab));
        XlsxHelper::larguras($s, [26, 12, 14, 12, 12, 12, 13, 16, 12, 12, 12]);
    }

    private static function transportadoras(Spreadsheet $x, $porTransp, $semCamioes): void
    {
        $s = XlsxHelper::folha($x, 'Transportadoras');
        $cab = ['Transportadora', 'Fora da lista prevista', 'Camiões', 'Confirmados', 'Atrelado normal', 'Atrelado link',
                'Sacos', 'Peso estimado (kg)', 'Peso líquido (kg)', 'Tempo médio (min)', 'Divergências'];
        XlsxHelper::titulo($s, 'Transportadoras', 'Camiões, pesos e tempos por transportadora.', count($cab));
        XlsxHelper::cabecalho($s, 4, $cab);

        $r = 5;
        foreach ($porTransp as $t) {
            XlsxHelper::escrever($s, $r, [
                $t['nome'], $t['extra'] ? 'Sim' : 'Não', (int) $t['camioes'], (int) $t['confirmados'],
                (int) $t['normal'], (int) $t['link'], (int) $t['sacos'],
                (float) $t['estimado_kg'], (float) $t['liquido_kg'],
                $t['tempo_medio'] !== null ? (int) $t['tempo_medio'] : null, (int) $t['divergencias'],
            ]);
            if ($t['extra']) {
                $s->getStyle("B{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFEDD5');
            }
            $r++;
        }
        $fim = $r - 1;
        XlsxHelper::formato($s, 5, $fim, array_fill_keys(range(3, 11), XlsxHelper::INT));
        XlsxHelper::grelha($s, 5, $fim, count($cab));

        XlsxHelper::celula($s, 1, $r, 'TOTAL');
        foreach ([3, 4, 5, 6, 7, 8, 9, 11] as $c) {
            XlsxHelper::soma($s, $r, $c, 5, $fim, XlsxHelper::INT);
        }
        XlsxHelper::totais($s, $r, count($cab));

        if ($semCamioes->isNotEmpty()) {
            XlsxHelper::celula($s, 1, $r + 2, 'Previstas na operação sem camiões: ' . $semCamioes->implode(', '));
        }
        XlsxHelper::tabela($s, 4, $fim, count($cab));
        XlsxHelper::larguras($s, [28, 14, 10, 12, 12, 12, 10, 16, 16, 12, 12]);
    }

    private static function camioes(Spreadsheet $x, $discharges): void
    {
        $s = XlsxHelper::folha($x, 'Camiões');
        $cab = ['Nº', 'Data', 'Guia', 'Motorista', 'Matrícula', 'Atrelado', 'Transportadora',
                'Saída do porto', 'Chegada à balança', 'Tempo (min)', 'Atraso (min)', 'Nível de atraso', 'Motivo do atraso',
                'Carga (porto → balança)', 'Sacos', 'Estimado (kg)', 'Bruto (kg)', 'Tara (kg)', 'Líquido (kg)', 'Dif. (kg)',
                'Estado', 'Divergência', 'Observações'];
        XlsxHelper::titulo($s, 'Detalhe dos camiões (' . $discharges->count() . ')', 'Dif. = líquido da balança − estimado no porto (só confirmados). Atraso: laranja a partir de 60 min, vermelho a partir de 80 min.', count($cab));
        XlsxHelper::cabecalho($s, 4, $cab);

        $qtd = fn ($v) => rtrim(rtrim(number_format((float) $v, 3, ',', '.'), '0'), ',');
        $nivelTxt = ['danger' => 'Vermelho', 'warn' => 'Laranja'];
        $r = 5;

        foreach ($discharges as $d) {
            $apos = in_array($d->status, ['in_transit', 'pending', 'weighing', 'confirmed'], true);
            $min = $apos ? $d->atrasoMinutos() : null;
            $nivel = $min === null ? '' : $d->nivelAtraso($min);
            $dif = $d->status === 'confirmed' && $d->peso_liquido !== null
                ? (float) $d->peso_liquido - (float) $d->peso_total_estimado : null;
            $dv = $d->hasDivergence();

            $carga = $d->items->map(function ($l) use ($qtd) {
                $t = ($l->operationItem->nome ?? '—') . ': ' . $qtd($l->quantidade);
                return $l->quantidade_confirmada !== null ? $t . ' → ' . $qtd($l->quantidade_confirmada) : $t;
            })->implode(' | ');

            XlsxHelper::escrever($s, $r, [
                (int) $d->id,
                XlsxHelper::dia($d->data),
                $d->numero_guia,
                $d->motorista,
                $d->matricula,
                ['normal' => 'Normal', 'link' => 'Link'][$d->tipo_atrelado] ?? null,
                $d->transportadora,
                $d->hora_saida_porto ? substr((string) $d->hora_saida_porto, 0, 5) : null,
                $d->hora_chegada_balanca ? substr((string) $d->hora_chegada_balanca, 0, 5) : null,
                $d->tempo_transporte !== null ? (int) $d->tempo_transporte : null,
                $min,
                $min === null ? null : ($nivelTxt[$nivel] ?? 'Normal'),
                $d->atrasoMotivoTexto() ?? ($d->atrasoSemJustificacao() ? 'Sem justificação' : null),
                $carga,
                (int) $d->total_sacos,
                (float) $d->peso_total_estimado,
                $d->peso_bruto !== null ? (float) $d->peso_bruto : null,
                $d->tara !== null ? (float) $d->tara : null,
                $d->peso_liquido !== null ? (float) $d->peso_liquido : null,
                $dif,
                Discharge::$statusLabels[$d->status] ?? $d->status,
                $dv ? 'Sim' : null,
                $d->observacoes,
            ]);
            $r++;
        }
        $fim = $r - 1;

        XlsxHelper::formato($s, 5, $fim, [
            1 => '0', 2 => XlsxHelper::DATA, 10 => XlsxHelper::INT, 11 => XlsxHelper::INT, 15 => XlsxHelper::INT,
            16 => XlsxHelper::INT, 17 => XlsxHelper::INT, 18 => XlsxHelper::INT, 19 => XlsxHelper::INT, 20 => XlsxHelper::DIF,
        ]);
        XlsxHelper::grelha($s, 5, $fim, count($cab));

        // a grelha pinta linhas alternadas: as cores de atraso e divergência vão por cima
        for ($i = 5; $i <= $fim; $i++) {
            $v = (string) $s->getCell("L{$i}")->getValue();
            if ($v === 'Vermelho') self::pintar($s, "L{$i}", 'FDECEA', 'B3261E');
            if ($v === 'Laranja') self::pintar($s, "L{$i}", 'FFEDD5', 'C2410C');
            if ($s->getCell("V{$i}")->getValue() === 'Sim') self::pintar($s, "V{$i}", 'FFF3CD', '856404');
        }

        XlsxHelper::celula($s, 1, $r, 'TOTAL');
        foreach ([15, 16, 17, 18, 19, 20] as $c) {
            XlsxHelper::soma($s, $r, $c, 5, $fim, $c === 20 ? XlsxHelper::DIF : XlsxHelper::INT);
        }
        XlsxHelper::totais($s, $r, count($cab));
        XlsxHelper::tabela($s, 4, $fim, count($cab));
        XlsxHelper::larguras($s, [7, 12, 14, 22, 14, 10, 24, 10, 11, 9, 10, 11, 26, 36, 8, 12, 11, 11, 12, 11, 13, 11, 30]);
    }

    private static function atrasos(Spreadsheet $x, $discharges): void
    {
        $s = XlsxHelper::folha($x, 'Atrasos e divergências');
        $cab = ['Nº', 'Data', 'Guia', 'Motorista', 'Matrícula', 'Divergência por produto (porto → balança)', 'Observações',
                'Atraso (min)', 'Motivo do atraso', 'Justificado por', 'Justificado em', 'Confirmado por'];
        XlsxHelper::titulo($s, 'Atrasos, divergências e observações', 'Só os camiões com alguma ocorrência.', count($cab));
        XlsxHelper::cabecalho($s, 4, $cab);

        $qtd = fn ($v) => rtrim(rtrim(number_format((float) $v, 3, ',', '.'), '0'), ',');
        $lista = $discharges
            ->filter(fn ($d) => $d->hasDivergence() || filled($d->observacoes) || $d->atrasoJustificado() || $d->atrasoSemJustificacao())
            ->sortBy('id');

        XlsxHelper::larguras($s, [8, 12, 14, 22, 14, 40, 30, 12, 26, 20, 17, 20]);

        if ($lista->isEmpty()) {
            XlsxHelper::celula($s, 1, 5, 'Sem divergências, atrasos nem observações registadas.');
            $s->getStyle('A5')->getFont()->getColor()->setRGB('198754');
            return;
        }

        $r = 5;
        foreach ($lista as $d) {
            $div = $d->items
                ->filter(fn ($l) => $l->quantidade_confirmada !== null && abs($l->quantidade_confirmada - $l->quantidade) > 0.0005)
                ->map(fn ($l) => ($l->operationItem->nome ?? '—') . ': ' . $qtd($l->quantidade) . ' → ' . $qtd($l->quantidade_confirmada)
                    . ' (' . ($l->quantidade_confirmada - $l->quantidade > 0 ? '+' : '') . $qtd($l->quantidade_confirmada - $l->quantidade) . ')')
                ->implode(' | ');

            XlsxHelper::escrever($s, $r, [
                (int) $d->id,
                XlsxHelper::dia($d->data),
                $d->numero_guia,
                $d->motorista,
                $d->matricula,
                $div ?: null,
                $d->observacoes,
                ($d->atrasoJustificado() || $d->atrasoSemJustificacao()) ? $d->atrasoMinutos() : null,
                $d->atrasoMotivoTexto() ?? ($d->atrasoSemJustificacao() ? 'Sem justificação' : null),
                $d->atrasoJustificadoPor?->name,
                XlsxHelper::dataHora($d->atraso_justificado_em),
                $d->confirmedBy->name ?? null,
            ]);
            $r++;
        }
        $fim = $r - 1;
        XlsxHelper::formato($s, 5, $fim, [1 => '0', 2 => XlsxHelper::DATA, 8 => XlsxHelper::INT, 11 => XlsxHelper::DATA_HORA]);
        XlsxHelper::grelha($s, 5, $fim, count($cab));
        $s->getStyle("F5:G{$fim}")->getAlignment()->setWrapText(true);
        XlsxHelper::tabela($s, 4, $fim, count($cab));
    }

    private static function historico(Spreadsheet $x, $historico): void
    {
        $s = XlsxHelper::folha($x, 'Histórico');
        XlsxHelper::titulo($s, 'Histórico da operação', 'Por ordem cronológica.', 2);
        XlsxHelper::cabecalho($s, 4, ['Data e hora', 'Evento']);

        $r = 5;
        foreach ($historico as $h) {
            XlsxHelper::escrever($s, $r, [XlsxHelper::dataHora($h['quando']), $h['texto']]);
            $r++;
        }
        XlsxHelper::formato($s, 5, $r - 1, [1 => XlsxHelper::DATA_HORA]);
        XlsxHelper::grelha($s, 5, $r - 1, 2);
        XlsxHelper::tabela($s, 4, $r - 1, 2);
        XlsxHelper::larguras($s, [18, 90]);
    }

    private static function pintar(Worksheet $s, string $celula, string $fundo, string $letra): void
    {
        $s->getStyle($celula)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($fundo);
        $s->getStyle($celula)->getFont()->setBold(true)->getColor()->setRGB($letra);
    }
}
