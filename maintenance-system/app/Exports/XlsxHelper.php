<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date as XlDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/** Estilo único para todos os Excel dos relatórios. */
final class XlsxHelper
{
    public const TZ = 'Africa/Maputo';
    public const ESCURO = '1E293B';
    public const VERMELHO = 'C60A1A';
    public const CINZA = 'F1F5F9';
    public const LINHA = 'E2E8F0';

    public const INT = '#,##0';
    public const T1 = '#,##0.0';
    public const T3 = '#,##0.000';
    public const DIF = '+#,##0;[Red]-#,##0;0';
    public const DATA = 'dd/mm/yyyy';
    public const DATA_HORA = 'dd/mm/yyyy hh:mm';

    public static function nova(): Spreadsheet
    {
        $x = new Spreadsheet();
        $x->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
        $x->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        return $x;
    }

    public static function folha(Spreadsheet $x, string $nome, bool $primeira = false): Worksheet
    {
        $s = $primeira ? $x->getActiveSheet() : $x->createSheet();
        $s->setTitle($nome);
        $s->setShowGridlines(false);
        $s->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToPage(true)
            ->setFitToWidth(1)
            ->setFitToHeight(0);

        return $s;
    }

    /** Escreve uma célula: texto sempre como texto (guias e matrículas não viram números). */
    public static function celula(Worksheet $s, int $col, int $linha, mixed $v): void
    {
        if ($v === null || $v === '') {
            return;
        }
        if (is_string($v)) {
            $s->setCellValueExplicit([$col, $linha], $v, DataType::TYPE_STRING);
        } else {
            $s->setCellValue([$col, $linha], $v);
        }
    }

    public static function escrever(Worksheet $s, int $linha, array $valores): void
    {
        foreach (array_values($valores) as $i => $v) {
            self::celula($s, $i + 1, $linha, $v);
        }
    }

    public static function dia($c): ?float
    {
        return $c ? XlDate::PHPToExcel(Carbon::parse($c)->startOfDay()) : null;
    }

    /** Data e hora convertidas para hora de Maputo. */
    public static function dataHora($c): ?float
    {
        return $c ? XlDate::PHPToExcel(Carbon::parse($c)->timezone(self::TZ)) : null;
    }

    public static function titulo(Worksheet $s, string $titulo, string $sub, int $nCols): void
    {
        $ult = Coordinate::stringFromColumnIndex(max($nCols, 2));
        $s->setCellValueExplicit('A1', $titulo, DataType::TYPE_STRING);
        $s->mergeCells("A1:{$ult}1");
        $s->getStyle('A1')->getFont()->setBold(true)->setSize(15)->getColor()->setRGB(self::ESCURO);
        $s->getRowDimension(1)->setRowHeight(26);

        $s->setCellValueExplicit('A2', $sub, DataType::TYPE_STRING);
        $s->mergeCells("A2:{$ult}2");
        $s->getStyle('A2')->getFont()->setSize(10)->getColor()->setRGB('64748B');
        $s->getStyle("A2:{$ult}2")->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setRGB(self::VERMELHO);
    }

    public static function cabecalho(Worksheet $s, int $linha, array $titulos): void
    {
        foreach (array_values($titulos) as $i => $t) {
            $s->setCellValueExplicit([$i + 1, $linha], $t, DataType::TYPE_STRING);
        }
        $ult = Coordinate::stringFromColumnIndex(count($titulos));
        $s->getStyle("A{$linha}:{$ult}{$linha}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::ESCURO]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $s->getRowDimension($linha)->setRowHeight(32);
    }

    /** Formatos por coluna: [indiceDaColuna => formato]. */
    public static function formato(Worksheet $s, int $de, int $ate, array $formatos): void
    {
        if ($ate < $de) {
            return;
        }
        foreach ($formatos as $col => $fmt) {
            $c = Coordinate::stringFromColumnIndex($col);
            $s->getStyle("{$c}{$de}:{$c}{$ate}")->getNumberFormat()->setFormatCode($fmt);
        }
    }

    /** Bordas finas e linhas alternadas. */
    public static function grelha(Worksheet $s, int $de, int $ate, int $nCols): void
    {
        if ($ate < $de) {
            return;
        }
        $ult = Coordinate::stringFromColumnIndex($nCols);
        $s->getStyle("A{$de}:{$ult}{$ate}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::LINHA]]],
        ]);
        for ($r = $de + 1; $r <= $ate; $r += 2) {
            $s->getStyle("A{$r}:{$ult}{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
        }
    }

    public static function totais(Worksheet $s, int $linha, int $nCols): void
    {
        $ult = Coordinate::stringFromColumnIndex($nCols);
        $s->getStyle("A{$linha}:{$ult}{$linha}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::CINZA]],
            'borders' => ['top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::ESCURO]]],
        ]);
    }

    public static function soma(Worksheet $s, int $linha, int $col, int $de, int $ate, ?string $fmt = null): void
    {
        if ($ate < $de) {
            return;
        }
        $c = Coordinate::stringFromColumnIndex($col);
        $s->setCellValue([$col, $linha], "=SUM({$c}{$de}:{$c}{$ate})");
        if ($fmt) {
            $s->getStyle("{$c}{$linha}")->getNumberFormat()->setFormatCode($fmt);
        }
    }

    public static function larguras(Worksheet $s, array $larguras): void
    {
        foreach (array_values($larguras) as $i => $w) {
            $s->getColumnDimensionByColumn($i + 1)->setWidth($w);
        }
    }

    /** Filtros nas colunas, cabeçalho fixo e repetido ao imprimir. */
    public static function tabela(Worksheet $s, int $cab, int $ate, int $nCols): void
    {
        $ult = Coordinate::stringFromColumnIndex($nCols);
        if ($ate > $cab) {
            $s->setAutoFilter("A{$cab}:{$ult}{$ate}");
        }
        $s->freezePane('A' . ($cab + 1));
        $s->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($cab, $cab);
    }

    /** Bloco "etiqueta / valor" (folhas de resumo). */
    public static function secao(Worksheet $s, int $linha, string $titulo, int $nCols = 2): int
    {
        $ult = Coordinate::stringFromColumnIndex($nCols);
        $s->setCellValueExplicit([1, $linha], mb_strtoupper($titulo), DataType::TYPE_STRING);
        $s->mergeCells("A{$linha}:{$ult}{$linha}");
        $s->getStyle("A{$linha}:{$ult}{$linha}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::ESCURO]],
        ]);
        $s->getRowDimension($linha)->setRowHeight(20);

        return $linha + 1;
    }

    public static function par(Worksheet $s, int $linha, string $rotulo, mixed $valor, ?string $fmt = null, ?string $cor = null): int
    {
        self::celula($s, 1, $linha, $rotulo);
        self::celula($s, 2, $linha, $valor ?? '—');
        $s->getStyle("A{$linha}")->getFont()->getColor()->setRGB('475569');
        $s->getStyle("B{$linha}")->getFont()->setBold(true);
        $s->getStyle("B{$linha}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        if ($fmt && is_numeric($valor)) {
            $s->getStyle("B{$linha}")->getNumberFormat()->setFormatCode($fmt);
        }
        if ($cor) {
            $s->getStyle("B{$linha}")->getFont()->getColor()->setRGB($cor);
        }
        $s->getStyle("A{$linha}:B{$linha}")->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB(self::LINHA);

        return $linha + 1;
    }

    public static function nome(string $prefixo, string $navio): string
    {
        return $prefixo . '_' . Str::slug($navio) . '_' . now(self::TZ)->format('Y-m-d_Hi') . '.xlsx';
    }

    public static function descarregar(Spreadsheet $x, string $nome)
    {
        $x->setActiveSheetIndex(0);

        return response()->streamDownload(function () use ($x) {
            (new Xlsx($x))->save('php://output');
        }, $nome, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
