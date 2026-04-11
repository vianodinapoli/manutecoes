<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 9pt;
        color: #1e293b;
        background: #fff;
        padding: 28px 32px 24px;
    }

    /* ── HEADER ── */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
    }
    .logo-side { display: flex; align-items: center; gap: 12px; }
    .logo-box {
        width: 48px; height: 48px;
        background: #c0392b;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 15pt; font-weight: 900;
        letter-spacing: -1px;
        font-family: Arial, sans-serif;
    }
    .company-name { font-size: 10.5pt; font-weight: 800; color: #c0392b; line-height: 1.25; }
    .company-meta { font-size: 6.8pt; color: #64748b; margin-top: 2px; line-height: 1.65; }
    .title-side { text-align: right; }
    .doc-title { font-size: 15pt; font-weight: 900; color: #c0392b; text-transform: uppercase; letter-spacing: .3px; }
    .doc-emitido { font-size: 7pt; color: #94a3b8; margin-top: 3px; }

    /* ── DIVIDER ── */
    .divider { border: none; border-top: 2px solid #c0392b; margin: 12px 0; }

    /* ── INFO BOX ── */
    .info-grid {
        display: table;
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        margin-bottom: 12px;
    }
    .info-row { display: table-row; }
    .info-cell {
        display: table-cell;
        padding: 8px 12px;
        border-right: 1px solid #e2e8f0;
        width: 33.33%;
    }
    .info-cell:last-child { border-right: none; }
    .info-cell-label { font-size: 6pt; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px; }
    .info-cell-value { font-size: 8.5pt; font-weight: 700; color: #1e293b; }

    /* ── KPI ROW — compact horizontal strip ── */
    .kpi-strip {
        display: table;
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        margin-bottom: 12px;
        background: #f8fafc;
    }
    .kpi-strip-row { display: table-row; }
    .kpi-cell {
        display: table-cell;
        padding: 7px 14px;
        border-right: 1px solid #e2e8f0;
        vertical-align: middle;
        width: 25%;
    }
    .kpi-cell:last-child { border-right: none; }
    .kpi-cell-label { font-size: 5.8pt; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; color: #94a3b8; margin-bottom: 1px; }
    .kpi-cell-value { font-size: 13pt; font-weight: 900; line-height: 1; }
    .kpi-green { color: #16a34a; }
    .kpi-red   { color: #dc2626; }
    .kpi-blue  { color: #1a56db; }
    .kpi-gray  { color: #64748b; }
    .kpi-accent-green { border-left: 3px solid #16a34a; }
    .kpi-accent-red   { border-left: 3px solid #dc2626; }
    .kpi-accent-blue  { border-left: 3px solid #1a56db; }
    .kpi-accent-gray  { border-left: 3px solid #64748b; }

    /* ── TABLE ── */
    table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    table.data-table thead th {
        font-size: 6.2pt;
        font-weight: 700;
        letter-spacing: .8px;
        text-transform: uppercase;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 7px 9px;
        text-align: left;
    }
    table.data-table thead th.right { text-align: right; }
    table.data-table tbody td {
        padding: 7px 9px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 8pt;
        color: #334155;
        vertical-align: middle;
    }
    table.data-table tbody td.right { text-align: right; }
    table.data-table tbody tr:last-child td { border-bottom: none; }
    table.data-table tbody tr:nth-child(even) td { background: #fafafa; }

    /* ── BADGES ── */
    .badge {
        display: inline-block;
        padding: 2px 7px;
        border-radius: 20px;
        font-size: 6.5pt;
        font-weight: 700;
        border: 1px solid;
    }
    .badge-entrada { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
    .badge-saida   { background: #fef2f2; color: #991b1b; border-color: #fecaca; }

    /* ── PRODUCT CELL ── */
    .prod-nome { font-weight: 700; font-size: 8pt; }
    .prod-ref  { font-size: 6.5pt; color: #94a3b8; margin-top: 1px; }

    /* ── TOTALS ── */
    .totals-wrap { text-align: right; }
    .totals-box {
        display: inline-block;
        width: 240px;
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        overflow: hidden;
    }
    .total-row {
        display: table;
        width: 100%;
        padding: 6px 13px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 8pt;
    }
    .total-row:last-child { border-bottom: none; }
    .total-row-label { display: table-cell; color: #64748b; text-align: left; }
    .total-row-value { display: table-cell; font-weight: 700; color: #1e293b; text-align: right; }
    .total-row.geral { background: #1e293b; }
    .total-row.geral .total-row-label { color: #94a3b8; font-size: 6.8pt; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
    .total-row.geral .total-row-value { color: #fff; font-size: 11pt; font-weight: 900; }

    /* ── FILTERS ── */
    .filters-row {
        font-size: 7pt;
        color: #64748b;
        margin-bottom: 10px;
        padding: 5px 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 5px;
    }
    .filters-row strong { color: #334155; }

    /* ── FOOTER ── */
    .footer {
        margin-top: 20px;
        padding-top: 8px;
        border-top: 1px solid #e2e8f0;
        display: table;
        width: 100%;
        font-size: 6.8pt;
        color: #94a3b8;
    }
    .footer-left  { display: table-cell; text-align: left;  line-height: 1.6; }
    .footer-right { display: table-cell; text-align: right; line-height: 1.6; }
</style>
</head>
<body>

    {{-- ── HEADER ── --}}
    <div class="header">
    <div class="company-block">
        <img src="{{ public_path('images/bymozelogo.png') }}" class="company-logo" alt="Logo" style="width: 60px; height: auto;">
        <div class="company-info">
            <div class="company-name">Fábrica de Explosivos de Moçambique</div>
            <div class="company-detail">
                Contribuinte Nº 400019029<br>
                Av. Samora Machel Nº — Parcela 10<br>
                Telef. +258 21 745 86/03 &nbsp;|&nbsp; FAX. +258 21 745 802
            </div>
        </div>
    </div>
</div>
        <div class="title-side">
            <div class="doc-title">Movimentos de Stock</div>
            <div class="doc-emitido">Emitido em {{ now()->format('d/m/Y') }} às {{ now()->format('H:i') }}</div>
        </div>
    </div>

    <hr class="divider">

    {{-- ── INFO BOX ── --}}
    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell">
                <div class="info-cell-label">Tipo</div>
                <div class="info-cell-value">{{ $filtros['tipo'] ? ucfirst($filtros['tipo']) : 'Todos' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">Período</div>
                <div class="info-cell-value">
                    @if($filtros['data_inicio'] ?? null)
                        {{ $filtros['data_inicio'] }} → {{ $filtros['data_fim'] ?? 'hoje' }}
                    @else
                        Todo o período
                    @endif
                </div>
            </div>
            <div class="info-cell">
                <div class="info-cell-label">Nº de Movimentos</div>
                <div class="info-cell-value">{{ $movimentos->count() }}</div>
            </div>
        </div>
    </div>

    {{-- ── KPI STRIP ── --}}
    <div class="kpi-strip">
        <div class="kpi-strip-row">
            <div class="kpi-cell kpi-accent-green">
                <div class="kpi-cell-label">Total Entradas</div>
                <div class="kpi-cell-value kpi-green">{{ $totalEntradas }}</div>
            </div>
            <div class="kpi-cell kpi-accent-red">
                <div class="kpi-cell-label">Total Saídas</div>
                <div class="kpi-cell-value kpi-red">{{ $totalSaidas }}</div>
            </div>
            <div class="kpi-cell kpi-accent-blue">
                <div class="kpi-cell-label">Movimentos Hoje</div>
                <div class="kpi-cell-value kpi-blue">{{ $movimentosHoje }}</div>
            </div>
            <div class="kpi-cell kpi-accent-gray">
                <div class="kpi-cell-label">Total Registos</div>
                <div class="kpi-cell-value kpi-gray">{{ $movimentos->count() }}</div>
            </div>
        </div>
    </div>

    {{-- ── FILTROS APLICADOS ── --}}
    @if(collect($filtros ?? [])->filter()->isNotEmpty())
    <div class="filters-row">
        <strong>Filtros:</strong>
        @if($filtros['tipo'] ?? null) Tipo: <strong>{{ ucfirst($filtros['tipo']) }}</strong> &nbsp;·&nbsp; @endif
        @if($filtros['responsavel'] ?? null) Responsável: <strong>{{ $filtros['responsavel'] }}</strong> &nbsp;·&nbsp; @endif
        @if($filtros['data_inicio'] ?? null) De: <strong>{{ $filtros['data_inicio'] }}</strong> @endif
        @if($filtros['data_fim'] ?? null) &nbsp;Até: <strong>{{ $filtros['data_fim'] }}</strong> @endif
    </div>
    @endif

    {{-- ── TABLE ── --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:30px;">#</th>
                <th>Produto / Material</th>
                <th style="width:70px;">Tipo</th>
                <th style="width:66px;" class="right">Qtd.</th>
                <th style="width:115px;">Responsável</th>
                <th>Observações</th>
                <th style="width:72px;" class="right">Data</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movimentos as $mov)
            <tr>
                <td style="color:#94a3b8;font-size:7pt;">{{ $mov->id }}</td>
                <td>
                    <div class="prod-nome">{{ $mov->stockItem->nome ?? '—' }}</div>
                    @if($mov->stockItem->referencia ?? null)
                    <div class="prod-ref">{{ $mov->stockItem->referencia }}</div>
                    @endif
                </td>
                <td>
                    @if($mov->tipo === 'entrada')
                        <span class="badge badge-entrada">&#9660; Entrada</span>
                    @else
                        <span class="badge badge-saida">&#9650; Saida</span>
                    @endif
                </td>
                <td class="right" style="font-weight:700;">
                    {{ number_format($mov->quantidade, 2, ',', '.') }}
                    @if($mov->stockItem->metadata['unidade'] ?? null)
                        <span style="font-size:6.5pt;color:#94a3b8;font-weight:400;">{{ $mov->stockItem->metadata['unidade'] }}</span>
                    @endif
                </td>
                <td>{{ $mov->responsavel }}</td>
                <td style="color:#64748b;font-size:7.5pt;">{{ $mov->observacoes ?: '—' }}</td>
                <td class="right">
                    <div style="font-weight:600;">{{ $mov->created_at->format('d/m/Y') }}</div>
                    <div style="font-size:6.5pt;color:#94a3b8;">{{ $mov->created_at->format('H:i') }}</div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:20px;color:#94a3b8;">
                    Nenhum movimento encontrado.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── TOTALS ── --}}
    <div class="totals-wrap">
        <div class="totals-box">
            <div class="total-row">
                <span class="total-row-label">Total Entradas</span>
                <span class="total-row-value" style="color:#16a34a;">{{ $totalEntradas }}</span>
            </div>
            <div class="total-row">
                <span class="total-row-label">Total Saídas</span>
                <span class="total-row-value" style="color:#dc2626;">{{ $totalSaidas }}</span>
            </div>
            <div class="total-row geral">
                <span class="total-row-label">Registos Totais</span>
                <span class="total-row-value">{{ $movimentos->count() }}</span>
            </div>
        </div>
    </div>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <div class="footer-left">
            <strong style="color:#334155;">DOCUMENTO GERADO POR</strong><br>
            {{ auth()->user()->name ?? 'Sistema' }} | {{ auth()->user()->email ?? '' }}
        </div>
        <div class="footer-right">
            Documento gerado automaticamente pelo sistema de gestão.<br>
            Não requer assinatura.
        </div>
    </div>

</body>
</html>