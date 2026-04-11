<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 9pt;
        color: #1e293b;
        background: #fff;
        padding: 28px 32px 24px;
    }

    /* ── HEADER ── */
    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px; }
    .logo-area { display: flex; align-items: center; gap: 10px; }
    .logo-box {
        width: 44px; height: 44px;
        background: #c0392b;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 14pt; font-weight: 900;
        letter-spacing: -1px;
    }
    .company-name { font-size: 11pt; font-weight: 800; color: #c0392b; line-height: 1.2; }
    .company-meta { font-size: 7pt; color: #64748b; margin-top: 2px; line-height: 1.6; }
    .doc-title-area { text-align: right; }
    .doc-title { font-size: 16pt; font-weight: 900; color: #c0392b; letter-spacing: .5px; text-transform: uppercase; }
    .doc-emitido { font-size: 7pt; color: #94a3b8; margin-top: 3px; }

    /* ── DIVIDER ── */
    .divider { border: none; border-top: 2px solid #c0392b; margin: 14px 0; }
    .divider-light { border: none; border-top: 1px solid #e2e8f0; margin: 12px 0; }

    /* ── INFO BOX ── */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 0;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 18px;
    }
    .info-cell {
        padding: 10px 14px;
        border-right: 1px solid #e2e8f0;
    }
    .info-cell:last-child { border-right: none; }
    .info-cell-label { font-size: 6.5pt; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #94a3b8; margin-bottom: 3px; }
    .info-cell-value { font-size: 9pt; font-weight: 700; color: #1e293b; }

    /* ── TABLE ── */
    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    thead th {
        font-size: 6.5pt;
        font-weight: 700;
        letter-spacing: .8px;
        text-transform: uppercase;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 8px 10px;
        text-align: left;
    }
    thead th.right { text-align: right; }
    tbody td {
        padding: 8px 10px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 8.5pt;
        color: #334155;
        vertical-align: middle;
    }
    tbody td.right { text-align: right; }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:nth-child(even) td { background: #fafafa; }

    /* ── BADGES ── */
    .badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 7pt;
        font-weight: 700;
        border: 1px solid;
    }
    .badge-entrada { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
    .badge-saida   { background: #fef2f2; color: #991b1b; border-color: #fecaca; }

    /* ── PRODUCT CELL ── */
    .prod-nome { font-weight: 700; font-size: 8.5pt; }
    .prod-ref  { font-size: 7pt; color: #94a3b8; margin-top: 1px; }

    /* ── TOTALS ── */
    .totals-wrap { display: flex; justify-content: flex-end; }
    .totals-box {
        width: 260px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }
    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 7px 14px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 8pt;
    }
    .total-row:last-child { border-bottom: none; }
    .total-row-label { color: #64748b; }
    .total-row-value { font-weight: 700; color: #1e293b; }
    .total-row.geral {
        background: #1e293b;
    }
    .total-row.geral .total-row-label { color: #94a3b8; font-size: 7.5pt; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; }
    .total-row.geral .total-row-value { color: #fff; font-size: 11pt; font-weight: 900; }

    /* ── KPI ROW ── */
    .kpi-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 18px;
    }
    .kpi-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        border-left: 3px solid;
    }
    .kpi-card.green { border-left-color: #16a34a; }
    .kpi-card.red   { border-left-color: #dc2626; }
    .kpi-card.blue  { border-left-color: #1a56db; }
    .kpi-card.gray  { border-left-color: #64748b; }
    .kpi-label { font-size: 6pt; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; color: #94a3b8; margin-bottom: 3px; }
    .kpi-value { font-size: 14pt; font-weight: 900; color: #1e293b; line-height: 1; }
    .kpi-card.green .kpi-value { color: #16a34a; }
    .kpi-card.red   .kpi-value { color: #dc2626; }
    .kpi-card.blue  .kpi-value { color: #1a56db; }

    /* ── FOOTER ── */
    .footer {
        margin-top: 24px;
        padding-top: 10px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        font-size: 7pt;
        color: #94a3b8;
    }
    .footer-left { line-height: 1.6; }
    .footer-right { text-align: right; line-height: 1.6; }

    /* ── FILTERS APPLIED ── */
    .filters-row {
        font-size: 7.5pt;
        color: #64748b;
        margin-bottom: 14px;
        padding: 7px 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
    }
    .filters-row strong { color: #334155; }
</style>
</head>
<body>

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="logo-area">
            <div class="logo-box">FEM</div>
            <div>
                <div class="company-name">Fábrica de Explosivos de Moçambique</div>
                <div class="company-meta">
                    Contribuinte Nº 400019029<br>
                    Av. Samora Machel Nº — Parcela 10<br>
                    Telef. +258 21 745 86/03 | FAX. +258 21 745 802
                </div>
            </div>
        </div>
        <div class="doc-title-area">
            <div class="doc-title">Movimentos de Stock</div>
            <div class="doc-emitido">Emitido em {{ now()->format('d/m/Y') }} às {{ now()->format('H:i') }}</div>
        </div>
    </div>

    <hr class="divider">

    {{-- ── INFO BOX ── --}}
    <div class="info-grid">
        <div class="info-cell">
            <div class="info-cell-label">Produto</div>
            <div class="info-cell-value">
                {{ $filtros['produto'] ?? 'Todos os Produtos' }}
            </div>
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

    {{-- ── KPI CARDS ── --}}
    <div class="kpi-row">
        <div class="kpi-card green">
            <div class="kpi-label">Total Entradas</div>
            <div class="kpi-value">{{ $totalEntradas }}</div>
        </div>
        <div class="kpi-card red">
            <div class="kpi-label">Total Saídas</div>
            <div class="kpi-value">{{ $totalSaidas }}</div>
        </div>
        <div class="kpi-card blue">
            <div class="kpi-label">Movimentos Hoje</div>
            <div class="kpi-value">{{ $movimentosHoje }}</div>
        </div>
        <div class="kpi-card gray">
            <div class="kpi-label">Registos (pág.)</div>
            <div class="kpi-value" style="color:#64748b;">{{ $movimentos->count() }}</div>
        </div>
    </div>

    {{-- ── FILTROS APLICADOS (se existirem) ── --}}
    @if(collect($filtros ?? [])->filter()->isNotEmpty())
    <div class="filters-row">
        <strong>Filtros aplicados:</strong>
        @if($filtros['tipo'] ?? null) Tipo: <strong>{{ ucfirst($filtros['tipo']) }}</strong> &nbsp;·&nbsp; @endif
        @if($filtros['responsavel'] ?? null) Responsável: <strong>{{ $filtros['responsavel'] }}</strong> &nbsp;·&nbsp; @endif
        @if($filtros['data_inicio'] ?? null) De: <strong>{{ $filtros['data_inicio'] }}</strong> @endif
        @if($filtros['data_fim'] ?? null) Até: <strong>{{ $filtros['data_fim'] }}</strong> @endif
    </div>
    @endif

    {{-- ── TABLE ── --}}
    <table>
        <thead>
            <tr>
                <th style="width:36px;">#</th>
                <th>Produto / Material</th>
                <th style="width:76px;">Tipo</th>
                <th style="width:72px;" class="right">Qtd.</th>
                <th style="width:120px;">Responsável</th>
                <th>Observações</th>
                <th style="width:80px;" class="right">Data</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movimentos as $mov)
            <tr>
                <td style="color:#94a3b8;font-size:7.5pt;">{{ $mov->id }}</td>
                <td>
                    <div class="prod-nome">{{ $mov->stockItem->nome ?? '—' }}</div>
                    @if($mov->stockItem->referencia ?? null)
                    <div class="prod-ref">{{ $mov->stockItem->referencia }}</div>
                    @endif
                </td>
                <td>
                    @if($mov->tipo === 'entrada')
                        <span class="badge badge-entrada">▼ Entrada</span>
                    @else
                        <span class="badge badge-saida">▲ Saída</span>
                    @endif
                </td>
                <td class="right" style="font-weight:700;">
                    {{ number_format($mov->quantidade, 2, ',', '.') }}
                    @if($mov->stockItem->metadata['unidade'] ?? null)
                        <span style="font-size:7pt;color:#94a3b8;font-weight:400;">{{ $mov->stockItem->metadata['unidade'] }}</span>
                    @endif
                </td>
                <td>{{ $mov->responsavel }}</td>
                <td style="color:#64748b;">{{ $mov->observacoes ?: '—' }}</td>
                <td class="right">
                    <div style="font-weight:600;">{{ $mov->created_at->format('d/m/Y') }}</div>
                    <div style="font-size:7pt;color:#94a3b8;">{{ $mov->created_at->format('H:i') }}</div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:24px;color:#94a3b8;">
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