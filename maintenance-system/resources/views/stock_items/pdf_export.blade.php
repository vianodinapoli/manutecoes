<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* ───────────────────────────────────────────
           RESET & BASE
           Nota: DomPDF não suporta var() — todos os
           valores são hard-coded directamente.
           Paleta: primary #1a3c5e · light #2563a8
        ─────────────────────────────────────────── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9.5px;
            color: #1e293b;
            background: #ffffff;
            padding: 28px 32px;
            line-height: 1.5;
        }

        /* ───────────────────────────────────────────
           HEADER
        ─────────────────────────────────────────── */
        .page-header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #1a3c5e;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .page-header-logo {
            display: table-cell;
            width: 110px;
            vertical-align: middle;
            padding-right: 16px;
            border-right: 1px solid #c8d8ec;
        }
        .page-header-logo img {
            display: block;
            max-width: 100px;
            max-height: 60px;
            width: auto;
            height: auto;
        }
        .page-header-info {
            display: table-cell;
            vertical-align: middle;
            padding-left: 18px;
        }
        .page-header-info h1 {
            font-size: 17px;
            font-weight: 700;
            color: #1a3c5e;
            letter-spacing: 0.3px;
        }
        .page-header-info p {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }
        .page-header-meta {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            white-space: nowrap;
        }
        .meta-badge {
            display: inline-block;
            background: #e8f0fa;
            border: 1px solid #c8d8ec;
            border-radius: 4px;
            padding: 4px 10px;
            font-size: 8.5px;
            color: #1a3c5e;
        }
        .meta-badge strong { font-size: 11px; display: block; }

        /* ───────────────────────────────────────────
           SUMMARY STRIP
        ─────────────────────────────────────────── */
        .summary-strip {
            display: table;
            width: 100%;
            margin-bottom: 18px;
            border: 1px solid #c8d8ec;
            border-radius: 4px;
            background: #f0f7ff;
            padding: 10px 14px;
        }
        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 0 16px;
            border-right: 1px solid #c8d8ec;
        }
        .summary-item:last-child { border-right: none; }
        .summary-item .s-label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary-item .s-value {
            font-size: 15px;
            font-weight: 700;
            color: #1a3c5e;
        }

        /* ───────────────────────────────────────────
           TABLE
        ─────────────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        col.col-artigo  { width: 26%; }
        col.col-ref     { width: 14%; }
        col.col-marca   { width: 14%; }
        col.col-qtd     { width: 8%;  }
        col.col-estado  { width: 12%; }
        col.col-armazem { width: 26%; }

        /* ── CABEÇALHO ── */
        thead tr th {
            background: #1a3c5e;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 8px 10px;
            border: none;
            text-align: left;
        }
        thead tr th.col-right  { text-align: right; }
        thead tr th.col-center { text-align: center; }

        /* ── GROUP HEADER ROW ── */
        tr.group-header td {
            background: #dbeafe;
            border-top: 2px solid #93c5fd;
            border-bottom: 1px solid #93c5fd;
            padding: 6px 10px;
            font-size: 9px;
            font-weight: 700;
            color: #2563a8;
        }
        tr.group-header td .group-name { font-size: 9.5px; }
        tr.group-header td .group-count {
            display: inline-block;
            background: #2563a8;
            color: #ffffff;
            border-radius: 10px;
            padding: 1px 7px;
            font-size: 7.5px;
            font-weight: 700;
            margin-left: 6px;
            vertical-align: middle;
        }
        tr.group-header td .group-total {
            float: right;
            font-size: 8.5px;
            color: #64748b;
            font-weight: 400;
        }
        tr.group-header td .group-total strong { color: #1a3c5e; }

        /* ── DETAIL ROWS ── */
        tr.item-row td {
            padding: 6px 10px 6px 18px;
            border-bottom: 1px solid #e8eef6;
            font-size: 9px;
            color: #1e293b;
            vertical-align: middle;
        }
        tr.item-row.even td { background: #f8fafc; }
        tr.item-row.odd  td { background: #ffffff; }
        tr.group-end td     { border-bottom: 2px solid #c8d8ec; }

        /* ── SINGLE ROWS ── */
        tr.single-row td {
            padding: 6px 10px;
            border-bottom: 1px solid #e8eef6;
            font-size: 9px;
            color: #1e293b;
            vertical-align: middle;
        }
        tr.single-row.even td { background: #f8fafc; }
        tr.single-row.odd  td { background: #ffffff; }

        td.col-ref     { font-family: "Courier New", monospace; font-size: 8.5px; color: #4a5568; }
        td.col-marca   { font-size: 8.5px; }
        td.col-qtd     { text-align: right; font-weight: 700; font-size: 10px; color: #1a3c5e; }
        td.col-armazem { color: #64748b; font-size: 8.5px; }

        /* ── ESTADO BADGE ── */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 7.5px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .badge-ok      { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .badge-aviso   { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .badge-critico { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .badge-default { background: #f1f5f9; color: #64748b;  border: 1px solid #cbd5e1; }

        /* ── TOTALS ROW ── */
        tr.totals-row td {
            background: #1a3c5e;
            color: #ffffff;
            font-weight: 700;
            font-size: 9.5px;
            padding: 8px 10px;
            border: none;
        }
        tr.totals-row td.col-qtd { text-align: right; color: #ffffff; }

        /* ───────────────────────────────────────────
           FOOTER
        ─────────────────────────────────────────── */
        .page-footer {
            margin-top: 32px;
            border-top: 2px solid #1a3c5e;
            padding-top: 14px;
            display: table;
            width: 100%;
        }
        .footer-left  { display: table-cell; width: 55%; vertical-align: top; }
        .footer-right { display: table-cell; vertical-align: top; text-align: right; }

        .footer-user-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 2px;
        }
        .footer-user-name  { font-size: 11px; font-weight: 700; color: #1a3c5e; }
        .footer-user-email { font-size: 8px; color: #64748b; }

        .signature-block { margin-top: 28px; }
        .signature-line  { border-top: 1px solid #94a3b8; width: 200px; margin-bottom: 4px; }
        .signature-caption { font-size: 8px; color: #64748b; }

        .footer-doc-info { font-size: 8px; color: #64748b; line-height: 1.8; }
        .footer-doc-info .doc-ref { font-size: 8.5px; font-weight: 700; color: #1a3c5e; }

        /* ── PAGE NUMBER ── */
        .page-number {
            text-align: center;
            margin-top: 10px;
            font-size: 7.5px;
            color: #64748b;
        }
    </style>
</head>
<body>

    {{-- ═══════════ HEADER ═══════════ --}}
    <div class="page-header">
        <div class="page-header-logo">
            <img src="{{ public_path('images/bymozelogo.png') }}" alt="Logo">
        </div>
        <div class="page-header-info">
            <h1>Mapa de Inventário / Stock</h1>
            <p>Documento oficial de controlo de existências em armazém</p>
        </div>
        <div class="page-header-meta">
            <div class="meta-badge">
                <strong>{{ date('d/m/Y') }}</strong>
                {{ date('H:i') }} &nbsp;·&nbsp; Extracção
            </div>
        </div>
    </div>

    {{-- ═══════════ AGRUPAMENTO — lógica PHP ═══════════ --}}
    {{--
        Agrupar os items pelo nome (campo `nome`).
        Items com mesmo nome mas referências diferentes aparecem
        numa linha de cabeçalho de grupo com sub-linhas indentadas.
        Items com nome único aparecem como linha simples.
    --}}
    @php
        $grouped      = $items->groupBy('nome');
        $totalQtd     = $items->sum('quantidade');
        $totalArtigos = $grouped->count();
        $totalRefs    = $items->count();

        // Helper: estado → classe CSS do badge
        function estadoBadgeClass(string $estado): string {
            $e = strtolower(trim($estado));
            if (in_array($e, ['ok','disponível','disponivel','normal','bom']))    return 'badge-ok';
            if (in_array($e, ['aviso','atenção','atencao','baixo','mínimo','minimo'])) return 'badge-aviso';
            if (in_array($e, ['crítico','critico','esgotado','indisponível','indisponivel','danificado'])) return 'badge-critico';
            return 'badge-default';
        }
    @endphp

    {{-- ═══════════ SUMMARY STRIP ═══════════ --}}
    <div class="summary-strip">
        <div class="summary-item">
            <div class="s-label">Total de Artigos</div>
            <div class="s-value">{{ $totalArtigos }}</div>
        </div>
        <div class="summary-item">
            <div class="s-label">Total de Referências</div>
            <div class="s-value">{{ $totalRefs }}</div>
        </div>
        <div class="summary-item">
            <div class="s-label">Quantidade Total</div>
            <div class="s-value">{{ number_format($totalQtd, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="s-label">Armazéns</div>
            <div class="s-value">{{ $items->pluck('numero_armazem')->unique()->count() }}</div>
        </div>
    </div>

    {{-- ═══════════ TABELA ═══════════ --}}
    <table>
        <colgroup>
            <col class="col-artigo">
            <col class="col-ref">
            <col class="col-marca">
            <col class="col-qtd">
            <col class="col-estado">
            <col class="col-armazem">
        </colgroup>
        <thead>
            <tr>
                <th>Artigo</th>
                <th>Referência</th>
                <th>Marca</th>
                <th class="col-right">Qtd.</th>
                <th class="col-center">Estado</th>
                <th>Armazém / Secção</th>
            </tr>
        </thead>
        <tbody>
            @php $rowIndex = 0; @endphp

            @foreach($grouped as $nome => $artigos)

                @if($artigos->count() > 1)
                    {{-- ── GRUPO (mesmo nome, várias referências) ── --}}
                    <tr class="group-header">
                        <td colspan="6">
                            <span class="group-name">{{ $nome }}</span>
                            <span class="group-count">{{ $artigos->count() }} refs.</span>
                            <span class="group-total">
                                Total &nbsp;<strong>{{ number_format($artigos->sum('quantidade'), 0, ',', '.') }}</strong> un.
                            </span>
                        </td>
                    </tr>

                    @foreach($artigos as $idx => $item)
                        @php
                            $isLast  = $idx === $artigos->count() - 1;
                            $rowClass = ($rowIndex % 2 === 0) ? 'even' : 'odd';
                            $rowIndex++;
                        @endphp
                        <tr class="item-row {{ $rowClass }}{{ $isLast ? ' group-end' : '' }}">
                            <td style="color:#64748b; font-style:italic;">↳ {{ $item->nome }}</td>
                            <td class="col-ref">{{ $item->referencia }}</td>
                            <td class="col-marca">{{ $item->marca_fabricante }}</td>
                            <td class="col-qtd">{{ number_format($item->quantidade, 0, ',', '.') }}</td>
                            <td style="text-align:center;">
                                <span class="badge {{ estadoBadgeClass($item->estado) }}">
                                    {{ $item->estado }}
                                </span>
                            </td>
                            <td class="col-armazem">
                                {{ $item->numero_armazem }}
                                @if($item->seccao_armazem)
                                    &mdash; {{ $item->seccao_armazem }}
                                @endif
                            </td>
                        </tr>
                    @endforeach

                @else
                    {{-- ── ARTIGO ÚNICO (sem agrupamento) ── --}}
                    @php
                        $item     = $artigos->first();
                        $rowClass = ($rowIndex % 2 === 0) ? 'even' : 'odd';
                        $rowIndex++;
                    @endphp
                    <tr class="single-row {{ $rowClass }}">
                        <td>{{ $item->nome }}</td>
                        <td class="col-ref">{{ $item->referencia }}</td>
                        <td class="col-marca">{{ $item->marca_fabricante }}</td>
                        <td class="col-qtd">{{ number_format($item->quantidade, 0, ',', '.') }}</td>
                        <td style="text-align:center;">
                            <span class="badge {{ estadoBadgeClass($item->estado) }}">
                                {{ $item->estado }}
                            </span>
                        </td>
                        <td class="col-armazem">
                            {{ $item->numero_armazem }}
                            @if($item->seccao_armazem)
                                &mdash; {{ $item->seccao_armazem }}
                            @endif
                        </td>
                    </tr>
                @endif

            @endforeach

            {{-- ── LINHA DE TOTAIS ── --}}
            <tr class="totals-row">
                <td colspan="3"><strong>TOTAL GERAL</strong></td>
                <td class="col-qtd">{{ number_format($totalQtd, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    {{-- ═══════════ FOOTER ═══════════ --}}
    <div class="page-footer">
        <div class="footer-left">
            <div class="footer-user-label">Extraído por</div>
            <div class="footer-user-name">{{ auth()->user()->name }}</div>
            <div class="footer-user-email">{{ auth()->user()->email }}</div>

            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-caption">Assinatura do Responsável</div>
            </div>
        </div>
        <div class="footer-right">
            <div class="footer-doc-info">
                <div class="doc-ref">Ref. Documento</div>
                INV-{{ date('Ymd') }}-{{ str_pad(auth()->id(), 4, '0', STR_PAD_LEFT) }}<br>
                <br>
                Data de Extracção<br>
                <strong>{{ date('d/m/Y \à\s H:i') }}</strong><br>
                <br>
                Este documento é gerado automaticamente<br>
                pelo sistema de gestão de inventário.
            </div>
        </div>
    </div>

    <div class="page-number">
        Página <span class="pagenum"></span>
    </div>

</body>
</html>