<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; background: #fff; padding: 28px 32px; }

        /* ── Cabeçalho ── */
        .doc-header { border-bottom: 2px solid #1e293b; padding-bottom: 14px; margin-bottom: 20px; }
        .doc-title { font-size: 15px; font-weight: 700; color: #1e293b; letter-spacing: .3px; }
        .doc-meta { font-size: 8.5px; color: #64748b; margin-top: 4px; letter-spacing: .5px; text-transform: uppercase; }

        /* ── Tabela ── */
        table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        thead th {
            font-size: 7.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #64748b;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            padding: 7px 10px;
            text-align: left;
        }
        tbody td {
            font-size: 9.5px;
            color: #334155;
            padding: 7px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:nth-child(even) td { background: #fafafa; }

        /* ── Status ── */
        .status {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: .3px;
        }
        .status.operacional { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .status.manutencao  { background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }
        .status.avariada    { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .status.desativada  { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

        /* ── Rodapé ── */
        .doc-footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: flex-end; }
        .footer-left { font-size: 8.5px; color: #64748b; }
        .footer-left strong { color: #1e293b; }
        .signature-block { text-align: center; }
        .signature-line { width: 160px; border-top: 1px solid #1e293b; margin-bottom: 5px; }
        .signature-label { font-size: 7.5px; color: #94a3b8; text-transform: uppercase; letter-spacing: .8px; }

        /* ── Sumário ── */
        .summary-row { display: flex; gap: 20px; margin-bottom: 18px; }
        .summary-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 14px; }
        .summary-item .s-lbl { font-size: 7px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #94a3b8; }
        .summary-item .s-val { font-size: 13px; font-weight: 800; color: #1e293b; }
    </style>
</head>
<body>

    {{-- CABEÇALHO --}}
    <div class="doc-header">
        <div class="doc-title">Listagem Geral de Máquinas e Equipamentos</div>
        <div class="doc-meta">
            Data de extracção: {{ date('d/m/Y H:i') }}
            &nbsp;·&nbsp;
            Extraído por: {{ auth()->user()->name }}
        </div>
    </div>

    {{-- SUMÁRIO --}}
    @php
        $total       = $items->count();
        $operacional = $items->where('status','Operacional')->count();
        $avariada    = $items->where('status','Avariada')->count();
        $desativada  = $items->where('status','Desativada')->count();
    @endphp
    <div class="summary-row">
        <div class="summary-item">
            <div class="s-lbl">Total</div>
            <div class="s-val">{{ $total }}</div>
        </div>
        <div class="summary-item">
            <div class="s-lbl">Operacionais</div>
            <div class="s-val">{{ $operacional }}</div>
        </div>
        <div class="summary-item">
            <div class="s-lbl">Avariadas</div>
            <div class="s-val">{{ $avariada }}</div>
        </div>
        <div class="summary-item">
            <div class="s-lbl">Desactivadas</div>
            <div class="s-val">{{ $desativada }}</div>
        </div>
    </div>

    {{-- TABELA --}}
    <table>
        <thead>
            <tr>
                <th>Nº Interno</th>
                <th>Tipo de Equipamento</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Nº Chassi</th>
                <th>Estado</th>
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
                <td><strong>{{ $machine->numero_interno }}</strong></td>
                <td>{{ $machine->tipo_equipamento }}</td>
                <td>{{ $machine->marca ?? '—' }}</td>
                <td>{{ $machine->modelo ?? '—' }}</td>
                <td style="color:#64748b;">{{ $machine->nr_chassi ?? '—' }}</td>
                <td>
                    <span class="status {{ $statusClass }}">{{ $machine->status }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- RODAPÉ --}}
    <div class="doc-footer">
        <div class="footer-left">
            <strong>BYMOZE - SG</strong><br>
            Documento gerado automaticamente em {{ date('d/m/Y \à\s H:i') }}
        </div>
        <div class="signature-block">
            <div class="signature-line"></div>
            <div class="signature-label">Responsável Técnico</div>
        </div>
    </div>

</body>
</html>