<?php

namespace App\Services;

use App\Models\Saida;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Modelo Excel para registar os camiões de uma saída, leitura do ficheiro
 * preenchido e exportações (lista de saídas e relatório de uma saída).
 *
 * Requer: composer require phpoffice/phpspreadsheet
 */
class SaidaExcel
{
    private const MAX_LINHAS = 1000;

    /* ════════════════════ MODELO PARA PREENCHER ════════════════════ */

    public function modelo(?Saida $saida = null): Spreadsheet
    {
        $ss = new Spreadsheet();
        $ws = $ss->getActiveSheet();
        $ws->setTitle('Camiões');

        $cabecalhos = ['Matrícula *', 'Atrelado', 'Nome *', 'Apelido', 'Empresa', 'Produto', 'Quantidade *', 'Telefone'];
        $ws->fromArray([$cabecalhos], null, 'A1');
        $this->estiloCabecalho($ws, 'A1:H1');

        foreach (['A' => 16, 'B' => 12, 'C' => 20, 'D' => 20, 'E' => 24, 'F' => 24, 'G' => 14, 'H' => 16] as $col => $w) {
            $ws->getColumnDimension($col)->setWidth($w);
        }
        $ws->freezePane('A2');

        // matrícula e telefone como texto (o Excel não deve "interpretar" os valores)
        $ws->getStyle('A2:A' . self::MAX_LINHAS)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        $ws->getStyle('H2:H' . self::MAX_LINHAS)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

        // lista: tipo de atrelado
        $this->listaSuspensa($ws, 'B', '"normal,link"');

        // instruções
        $in = $ss->createSheet();
        $in->setTitle('Instruções');
        $linhas = [
            ['COMO PREENCHER'],
            [''],
            ['1. Preencha uma linha por camião, na folha "Camiões", a partir da linha 2.'],
            ['2. Os campos com * são obrigatórios: Matrícula, Nome e Quantidade.'],
            ['3. Atrelado: normal ou link (opcional).'],
            ['4. Empresa: se ficar vazia, usa-se o cliente da saída.'],
            ['5. Produto: escolha da lista (opcional). Se ficar vazio, escolhe-se no momento de carregar.'],
            ['6. Quantidade: número positivo. Em sacos tem de ser inteiro.'],
            ['7. Qualquer coluna extra que acrescentar (ex.: Telefone, Cabine, Observação) é guardada em "Outros dados".'],
            ['8. Uma matrícula só pode aparecer uma vez por ficheiro e não pode já estar na fila desta saída.'],
            ['9. Camião que não estiver registado não pode carregar.'],
            [''],
            ['EXEMPLO'],
            ['Matrícula', 'Atrelado', 'Nome', 'Apelido', 'Empresa', 'Produto', 'Quantidade', 'Telefone'],
            ['ABC 123 MC', 'link', 'João', 'Macamo', 'Agro Sul, Lda', '', '30', '84 000 0000'],
        ];
        $in->fromArray($linhas, null, 'A1');
        $in->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $in->getStyle('A13')->getFont()->setBold(true);
        $in->getStyle('A14:H14')->getFont()->setBold(true);
        $in->getColumnDimension('A')->setWidth(18);

        // lista: produtos da operação de origem (folha escondida)
        $nomes = $saida?->operation?->items?->pluck('nome')->values() ?? collect();
        if ($nomes->isNotEmpty()) {
            $ls = $ss->createSheet();
            $ls->setTitle('Listas');
            foreach ($nomes as $i => $n) {
                $ls->setCellValue('A' . ($i + 1), $n);
            }
            $ls->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
            $this->listaSuspensa($ws, 'F', 'Listas!$A$1:$A$' . $nomes->count());
        }

        $ss->setActiveSheetIndex(0);

        return $ss;
    }

    private function listaSuspensa(Worksheet $ws, string $coluna, string $formula): void
    {
        $v = new DataValidation();
        $v->setType(DataValidation::TYPE_LIST);
        $v->setErrorStyle(DataValidation::STYLE_STOP);
        $v->setAllowBlank(true);
        $v->setShowDropDown(true);
        $v->setShowErrorMessage(true);
        $v->setErrorTitle('Valor inválido');
        $v->setError('Escolha um valor da lista.');
        $v->setFormula1($formula);

        for ($r = 2; $r <= 500; $r++) {
            $ws->getCell($coluna . $r)->setDataValidation(clone $v);
        }
    }

    /* ════════════════════ LEITURA DO FICHEIRO PREENCHIDO ════════════════════ */

    /**
     * Lê o ficheiro e devolve ['linhas' => registos válidos, 'erros' => [['linha'=>n,'msg'=>...]]].
     * Linhas inválidas não entram: o resto é importado e os erros são mostrados.
     */
    public function ler(UploadedFile $file, Saida $saida): array
    {
        $ss = IOFactory::load($file->getRealPath());
        $ws = $ss->getSheetByName('Camiões') ?? $ss->getSheet(0);
        $dados = $ws->toArray(null, true, true, false);

        if (count($dados) < 2) {
            throw new InvalidArgumentException('O ficheiro não tem linhas de camiões.');
        }
        if (count($dados) > self::MAX_LINHAS + 1) {
            throw new InvalidArgumentException('O ficheiro tem mais de ' . self::MAX_LINHAS . ' linhas.');
        }

        // ── cabeçalhos ──
        $mapa = [
            'matricula' => 'matricula', 'atrelado' => 'tipo_atrelado', 'tipoatrelado' => 'tipo_atrelado',
            'nome' => 'nome', 'motorista' => 'nome', 'apelido' => 'apelido',
            'empresa' => 'empresa', 'cliente' => 'empresa', 'produto' => 'produto',
            'quantidade' => 'quantidade', 'qtd' => 'quantidade',
        ];
        $colunas = [];   // índice da coluna => campo conhecido
        $extras  = [];   // índice da coluna => título (vai para "outros dados")
        foreach ($dados[0] as $i => $titulo) {
            $titulo = trim((string) $titulo);
            if ($titulo === '') {
                continue;
            }
            $chave = $this->normalizar($titulo);
            if (isset($mapa[$chave])) {
                $colunas[$i] = $mapa[$chave];
            } else {
                $extras[$i] = trim(str_replace('*', '', $titulo));
            }
        }
        $faltam = array_diff(['matricula', 'nome', 'quantidade'], array_values($colunas));
        if ($faltam) {
            throw new InvalidArgumentException('Cabeçalhos em falta: ' . implode(', ', $faltam) . '. Use o modelo Excel.');
        }

        // ── contexto ──
        $itens = ($saida->operation?->items ?? collect())
            ->mapWithKeys(fn ($i) => [$this->normalizar($i->nome) => $i]);
        $jaNaFila = $saida->camioes()->pluck('matricula')
            ->map(fn ($m) => $this->normMat((string) $m))->filter()->flip();
        $vistas = [];

        $linhas = [];
        $erros  = [];

        foreach (array_slice($dados, 1, null, true) as $idx => $linha) {
            $n = $idx + 1; // número da linha no Excel
            $r = [];
            foreach ($colunas as $i => $campo) {
                $r[$campo] = trim((string) ($linha[$i] ?? ''));
            }
            $meta = [];
            foreach ($extras as $i => $titulo) {
                $v = trim((string) ($linha[$i] ?? ''));
                if ($v !== '') {
                    $meta[$titulo] = $v;
                }
            }

            // linha totalmente vazia
            if (! array_filter($r, fn ($v) => $v !== '') && ! $meta) {
                continue;
            }

            $matricula = strtoupper($r['matricula'] ?? '');
            $nome      = $r['nome'] ?? '';
            $q         = $this->numero($r['quantidade'] ?? '');
            $atrelado  = strtolower($r['tipo_atrelado'] ?? '');
            $produto   = $r['produto'] ?? '';

            if ($matricula === '')                         { $erros[] = ['linha' => $n, 'msg' => 'Matrícula em falta.']; continue; }
            if ($nome === '')                              { $erros[] = ['linha' => $n, 'msg' => "Nome em falta ({$matricula})."]; continue; }
            if ($q === null || $q <= 0)                    { $erros[] = ['linha' => $n, 'msg' => "Quantidade inválida ({$matricula})."]; continue; }
            if ($atrelado !== '' && ! in_array($atrelado, ['normal', 'link'], true)) {
                $erros[] = ['linha' => $n, 'msg' => "Atrelado deve ser normal ou link ({$matricula})."]; continue;
            }

            $chaveMat = $this->normMat($matricula);
            if (isset($vistas[$chaveMat]))                 { $erros[] = ['linha' => $n, 'msg' => "Matrícula repetida no ficheiro ({$matricula}), já na linha {$vistas[$chaveMat]}."]; continue; }
            if (isset($jaNaFila[$chaveMat]))               { $erros[] = ['linha' => $n, 'msg' => "{$matricula} já consta nesta saída (cada camião carrega uma vez por lista)."]; continue; }

            $itemId = null;
            if ($produto !== '') {
                $item = $itens[$this->normalizar($produto)] ?? null;
                if (! $item) {
                    $erros[] = ['linha' => $n, 'msg' => "Produto \"{$produto}\" não existe na operação de origem ({$matricula})."]; continue;
                }
                if (($item->unidade ?? null) === 'saco' && floor($q) != $q) {
                    $erros[] = ['linha' => $n, 'msg' => "Quantidade em sacos tem de ser inteira ({$matricula})."]; continue;
                }
                $itemId = $item->id;
            }

            $vistas[$chaveMat] = $n;
            $linhas[] = [
                'matricula'         => $matricula,
                'tipo_atrelado'     => $atrelado ?: null,
                'nome'              => $nome,
                'apelido'           => ($r['apelido'] ?? '') !== '' ? $r['apelido'] : null,
                'empresa'           => ($r['empresa'] ?? '') !== '' ? $r['empresa'] : $saida->cliente,
                'quantidade'        => $q,
                'operation_item_id' => $itemId,
                'metadata'          => $meta,
            ];
        }

        return ['linhas' => $linhas, 'erros' => $erros];
    }

    /* ════════════════════ EXPORTAÇÕES ════════════════════ */

    /**
     * Lista de saídas (com os filtros aplicados) + totais por produto.
     *
     * @param Collection $saidas       saídas com operation.items e contadores
     * @param Collection $porSaidaItem registos [saida_id, operation_item_id, total] carregados
     * @param Collection $itemMap      id => item (nome, unidade_label)
     */
    public function listaSaidas(Collection $saidas, Collection $porSaidaItem, Collection $itemMap): Spreadsheet
    {
        $ss = new Spreadsheet();
        $ws = $ss->getActiveSheet();
        $ws->setTitle('Saídas');

        $ws->fromArray([[
            'Código', 'Data', 'Cliente', 'Operação (navio)', 'Estado',
            'Camiões registados', 'Carregados', 'Por carregar', 'Carregado (por produto)',
        ]], null, 'A1');
        $this->estiloCabecalho($ws, 'A1:I1');

        $porSaida = $porSaidaItem->groupBy('saida_id');
        $r = 2;
        foreach ($saidas as $s) {
            $partes = ($porSaida[$s->id] ?? collect())->map(function ($x) use ($itemMap) {
                $it = $itemMap[$x->operation_item_id] ?? null;
                return ($it->nome ?? '—') . ': ' . $this->fmt($x->total) . ' ' . ($it->unidade_label ?? '');
            })->implode('; ');

            $ws->fromArray([[
                $s->codigo,
                $s->data?->format('d/m/Y'),
                $s->cliente,
                $s->operation->navio ?? '—',
                $s->status === 'fechada' ? 'Fechada' : 'Aberta',
                $s->camioes_count,
                $s->carregados_count,
                $s->pendentes_count,
                $partes,
            ]], null, 'A' . $r++);
        }
        foreach (range('A', 'I') as $c) {
            $ws->getColumnDimension($c)->setAutoSize(true);
        }
        $ws->freezePane('A2');

        // totais por produto
        $p = $ss->createSheet();
        $p->setTitle('Por produto');
        $p->fromArray([['Produto', 'Unidade', 'Total carregado']], null, 'A1');
        $this->estiloCabecalho($p, 'A1:C1');
        $r = 2;
        foreach ($porSaidaItem->groupBy('operation_item_id') as $itemId => $g) {
            $it = $itemMap[$itemId] ?? null;
            $p->fromArray([[$it->nome ?? '—', $it->unidade_label ?? '', (float) $g->sum('total')]], null, 'A' . $r++);
        }
        foreach (range('A', 'C') as $c) {
            $p->getColumnDimension($c)->setAutoSize(true);
        }

        $ss->setActiveSheetIndex(0);

        return $ss;
    }

    /** Relatório de uma saída: camiões + resumo por produto. */
    public function relatorioSaida(Saida $saida, Collection $camioes, Collection $itens, Collection $resumo): Spreadsheet
    {
        $ss = new Spreadsheet();
        $ws = $ss->getActiveSheet();
        $ws->setTitle('Camiões');

        $ws->fromArray([[
            '#', 'Matrícula', 'Atrelado', 'Empresa', 'Motorista', 'Outros dados',
            'Produto', 'Pedido', 'Carregado', 'Estado', 'Carregado em', 'Carregado por',
        ]], null, 'A1');
        $this->estiloCabecalho($ws, 'A1:L1');

        $r = 2;
        foreach ($camioes as $c) {
            $it  = $itens[$c->operation_item_id] ?? null;
            $car = $c->status === 'carregada';
            $ws->fromArray([[
                $c->id,
                $c->matricula,
                $c->tipo_atrelado ? strtoupper($c->tipo_atrelado) : '',
                $c->empresa,
                $c->nome_completo,
                collect($c->metadata ?? [])->map(fn ($v, $k) => "{$k}: {$v}")->implode('; '),
                $it->nome ?? '',
                (float) ($c->quantidade_pedida ?? $c->quantidade),
                $car ? (float) $c->quantidade : '',
                $car ? 'Carregado' : 'Por carregar',
                $c->carregado_em?->format('d/m/Y H:i'),
                $c->carregadoPor->name ?? '',
            ]], null, 'A' . $r++);
        }
        foreach (range('A', 'L') as $col) {
            $ws->getColumnDimension($col)->setAutoSize(true);
        }
        $ws->freezePane('A2');

        $rs = $ss->createSheet();
        $rs->setTitle('Resumo');
        $rs->fromArray([
            ['Saída', $saida->codigo],
            ['Cliente', $saida->cliente],
            ['Operação', $saida->operation->navio ?? '—'],
            ['Data', $saida->data?->format('d/m/Y')],
            ['Estado', $saida->isFechada() ? 'Fechada' : 'Aberta'],
            [],
            ['Produto', 'Unidade', 'Pedido', 'Carregado'],
        ], null, 'A1');
        $rs->getStyle('A1:A5')->getFont()->setBold(true);
        $this->estiloCabecalho($rs, 'A7:D7');
        $r = 8;
        foreach ($resumo as $x) {
            $rs->fromArray([[$x['nome'], $x['label'], $x['pedido'], $x['carregado']]], null, 'A' . $r++);
        }
        foreach (range('A', 'D') as $col) {
            $rs->getColumnDimension($col)->setAutoSize(true);
        }

        $ss->setActiveSheetIndex(0);

        return $ss;
    }

    public function download(Spreadsheet $ss, string $nome): StreamedResponse
    {
        return response()->streamDownload(function () use ($ss) {
            IOFactory::createWriter($ss, 'Xlsx')->save('php://output');
        }, $nome, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /* ════════════════════ AUXILIARES ════════════════════ */

    private function estiloCabecalho(Worksheet $ws, string $intervalo): void
    {
        $ws->getStyle($intervalo)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
        ]);
    }

    private function normalizar(string $s): string
    {
        return Str::of($s)->ascii()->lower()->replaceMatches('/[^a-z0-9]/', '')->toString();
    }

    private function normMat(string $m): string
    {
        return strtoupper(preg_replace('/\s+/', '', trim($m)));
    }

    private function numero(string $v): ?float
    {
        $v = str_replace([' ', "\u{00A0}"], '', $v);
        if ($v === '') {
            return null;
        }
        if (str_contains($v, ',') && str_contains($v, '.')) {
            $v = str_replace('.', '', $v);
        }
        $v = str_replace(',', '.', $v);

        return is_numeric($v) ? (float) $v : null;
    }

    private function fmt($n): string
    {
        return rtrim(rtrim(number_format((float) $n, 3, ',', '.'), '0'), ',');
    }
}