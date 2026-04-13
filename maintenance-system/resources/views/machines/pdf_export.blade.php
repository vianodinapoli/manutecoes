<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; background: #fff; padding: 28px 32px; }

        /* ── Cabeçalho empresa ── */
        .doc-header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #c60a1a;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .header-logo-col {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
        }
        .header-meta-col {
            display: table-cell;
            width: 40%;
            vertical-align: middle;
            text-align: right;
        }
        .company-name {
            font-size: 13px;
            font-weight: 700;
            color: #c60a1a;
            letter-spacing: .4px;
            text-transform: uppercase;
        }
        .company-sub {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 2px;
            letter-spacing: .3px;
        }
        .doc-title {
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
        }
        .doc-meta {
            font-size: 7.5px;
            color: #94a3b8;
            letter-spacing: .4px;
            text-transform: uppercase;
        }

        /* ── Faixa colorida ── */
        .accent-bar {
            background: #c60a1a;
            height: 3px;
            border-radius: 2px;
            margin-bottom: 16px;
        }

        /* ── Sumário ── */
        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 18px;
        }
        .summary-cell {
            display: table-cell;
            width: 25%;
            padding-right: 10px;
        }
        .summary-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            padding: 8px 12px;
            border-left: 3px solid #c60a1a;
        }
        .s-lbl {
            font-size: 6.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 2px;
        }
        .s-val {
            font-size: 14px;
            font-weight: 800;
            color: #1e293b;
        }
        .summary-item.green { border-left-color: #16a34a; }
        .summary-item.red   { border-left-color: #dc2626; }
        .summary-item.slate { border-left-color: #475569; }

        /* ── Tabela ── */
        table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        thead tr {
            background: #1e293b;
        }
        thead th {
            font-size: 7px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #fff;
            padding: 8px 10px;
            text-align: left;
        }
        tbody td {
            font-size: 9px;
            color: #334155;
            padding: 7px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:nth-child(even) td { background: #f8fafc; }

        /* ── Número interno badge ── */
        .badge-num {
            display: inline-block;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 2px 7px;
            font-size: 8px;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: .4px;
        }

        /* ── Status badges ── */
        .status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 7.5px;
            font-weight: 700;
            letter-spacing: .3px;
        }
        .status.operacional { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .status.manutencao  { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .status.avariada    { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .status.desativada  { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

        /* ── Rodapé ── */
        .doc-footer {
            margin-top: 28px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            display: table;
            width: 100%;
        }
        .footer-left {
            display: table-cell;
            vertical-align: bottom;
            font-size: 8px;
            color: #64748b;
        }
        .footer-left strong { color: #1e293b; }
        .footer-right {
            display: table-cell;
            vertical-align: bottom;
            text-align: center;
            width: 180px;
        }
        .signature-line { border-top: 1px solid #1e293b; margin-bottom: 5px; }
        .signature-label { font-size: 7px; color: #94a3b8; text-transform: uppercase; letter-spacing: .8px; }

        /* ── Total registos ── */
        .section-header {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 7px 7px 0 0;
            padding: 7px 12px;
            display: table;
            width: 100%;
            margin-bottom: 0;
        }
        .section-header-left {
            display: table-cell;
            vertical-align: middle;
            font-size: 8px;
            font-weight: 700;
            color: #334155;
            letter-spacing: .3px;
            text-transform: uppercase;
        }
        .section-header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            font-size: 7.5px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    {{-- CABEÇALHO --}}
    <div class="doc-header">
        <div class="header-logo-col">
            <div class="company-name">Fábrica de Explosivos de Moçambique</div>
            <div class="company-sub">NUIT 400019029 &nbsp;·&nbsp; Av. Samora Machel, Beira &nbsp;·&nbsp; +258 21 745 86/03</div>
        </div>
        <div class="header-meta-col">
            <div class="doc-title">Listagem de Máquinas e Equipamentos</div>
            <div class="doc-meta">
                Extracção: {{ date('d/m/Y H:i') }}
                &nbsp;·&nbsp;
                Por: {{ auth()->user()->name }}
            </div>
        </div>
    </div>

    {{-- SUMÁRIO --}}
    @php
        $total       = $items->count();
        $operacional = $items->where('status', 'Operacional')->count();
        $avariada    = $items->where('status', 'Avariada')->count();
        $desativada  = $items->where('status', 'Desativada')->count();
    @endphp
    <div class="summary-row">
        <div class="summary-cell">
            <div class="summary-item">
                <div class="s-lbl">Total</div>
                <div class="s-val">{{ $total }}</div>
            </div>
        </div>
        <div class="summary-cell">
            <div class="summary-item green">
                <div class="s-lbl">Operacionais</div>
                <div class="s-val">{{ $operacional }}</div>
            </div>
        </div>
        <div class="summary-cell">
            <div class="summary-item red">
                <div class="s-lbl">Avariadas</div>
                <div class="s-val">{{ $avariada }}</div>
            </div>
        </div>
        <div class="summary-cell" style="padding-right:0;">
            <div class="summary-item slate">
                <div class="s-lbl">Desactivadas</div>
                <div class="s-val">{{ $desativada }}</div>
            </div>
        </div>
    </div>

    {{-- CABEÇALHO DA SECÇÃO --}}
    <div class="section-header">
        <div class="section-header-left">Inventário de Equipamentos</div>
        <div class="section-header-right">{{ $total }} registo(s) encontrado(s)</div>
    </div>

    {{-- TABELA --}}
    <table>
        <thead>
            <tr>
                <th style="width:90px;">Nº Interno</th>
                <th>Tipo de Equipamento</th>
                <th style="width:100px;">Marca</th>
                <th style="width:100px;">Modelo</th>
                <th style="width:110px;">Nº Chassi</th>
                <th style="width:90px;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $machine)
            @php
                $statusClass = match($machine->status) {
                    'Operacional'   => 'operacional',
                    'Em Manutenção' => 'manutencao',
                    'Avariada'      => 'avariada',
                    'Desativada'    => 'desativada',
                    default         => 'desativada',
                };
            @endphp
            <tr>
                <td><span class="badge-num">{{ $machine->numero_interno }}</span></td>
                <td><strong>{{ $machine->tipo_equipamento }}</strong></td>
                <td>{{ $machine->marca ?? '—' }}</td>
                <td>{{ $machine->modelo ?? '—' }}</td>
                <td style="color:#94a3b8;font-size:8.5px;">{{ $machine->nr_chassi ?? '—' }}</td>
                <td><span class="status {{ $statusClass }}">{{ $machine->status }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- RODAPÉ --}}
    <div class="doc-footer">
        <div class="footer-left">
            <strong>FEM — Fábrica de Explosivos de Moçambique</strong><br>
            Documento gerado automaticamente em {{ date('d/m/Y \à\s H:i') }}<br>
            <span style="color:#c60a1a;font-size:7px;font-weight:700;">DOCUMENTO INTERNO — USO RESTRITO</span>
        </div>
        <div class="footer-right">
            <div class="signature-line"></div>
            <div class="signature-label">Responsável Técnico</div>
        </div>
    </div>

</body>
</html>