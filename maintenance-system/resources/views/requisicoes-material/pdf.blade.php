<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Requisição de Material #{{ str_pad($requisicao->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; padding: 30px; }

        /* ── Cabeçalho ── */
        .header { display: flex; justify-content: space-between; align-items: flex-start;
                  margin-bottom: 20px; border-bottom: 3px solid #c60a1a; padding-bottom: 14px; }
        .company-block  { display: flex; align-items: flex-start; gap: 14px; }
        .company-logo   { width: 72px; height: auto; }
        .company-name   { font-size: 13px; font-weight: bold; color: #c60a1a; line-height: 1.3; }
        .company-detail { font-size: 9px; color: #555; margin-top: 3px; line-height: 1.6; }
        .doc-title      { text-align: right; }
        .doc-title h1   { font-size: 18px; font-weight: bold; color: #c60a1a; }
        .doc-title .doc-num  { font-size: 13px; color: #444; margin-top: 4px; }
        .doc-title .doc-date { font-size: 11px; color: #888; margin-top: 2px; }

        /* ── Badge de estado ── */
        .status-badge {
            display: inline-block; margin-top: 6px;
            padding: 3px 12px; border-radius: 20px;
            font-size: 10px; font-weight: bold; letter-spacing: .5px; text-transform: uppercase;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .status-EMITIDA    { background: #e0f2fe; color: #0369a1; }
        .status-CONFIRMADA { background: #fef9c3; color: #a16207; }
        .status-FINALIZADA { background: #dcfce7; color: #15803d; }
        .status-CANCELADO  { background: #fee2e2; color: #dc2626; }

        /* ── Info cards ── */
        .info-box  { display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
        .info-card { flex: 1; background: #fdf2f2; border-left: 3px solid #c60a1a;
                     padding: 8px 12px; border-radius: 3px; min-width: 120px;
                     -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .info-card .label { font-size: 9px; text-transform: uppercase; color: #888;
                            font-weight: bold; margin-bottom: 3px; letter-spacing: .5px; }
        .info-card .value { font-size: 11px; font-weight: bold; color: #222; }
        .info-card .sub   { font-size: 10px; color: #555; margin-top: 2px; }

        /* ── Guia badge ── */
        .guia-badge {
            display: inline-block; background: #0f172a; color: #fff;
            padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: bold;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }

        /* ── Tabela de itens ── */
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        thead tr { background: #c60a1a; color: white;
                   -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        thead th { padding: 7px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: .5px; }
        thead th.right  { text-align: right; }
        thead th.center { text-align: center; }
        tbody tr { border-bottom: 1px solid #e8edf2; }
        tbody tr:nth-child(even) { background: #fdf5f5;
                   -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        tbody td { padding: 7px 10px; font-size: 11px; }
        tbody td.right  { text-align: right; }
        tbody td.center { text-align: center; }

        /* ── Observações ── */
        .obs-box { background: #f8f9fa; border-left: 3px solid #c60a1a;
                   padding: 8px 12px; border-radius: 3px; margin-bottom: 16px; font-size: 11px;
                   -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .obs-box .obs-label { font-size: 9px; text-transform: uppercase; color: #888;
                              font-weight: bold; margin-bottom: 3px; letter-spacing: .5px; }

        /* ── Secção de descarga em GRID 2×2 ── */
        .descarga-section {
            margin-top: 16px;
            border: 2px solid #0f172a;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 16px;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .descarga-header {
            background: #0f172a; color: #fff;
            padding: 7px 14px;
            font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: .8px;
            display: flex; justify-content: space-between; align-items: center;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }

        /* Grid 2×2 para os dados de confirmação */
        .descarga-grid {
            display: table;
            width: 100%;
        }
        .descarga-row {
            display: table-row;
        }
        .descarga-cell {
            display: table-cell;
            width: 50%;
            padding: 10px 14px;
            border-right: 1px solid #e8edf2;
            border-bottom: 1px solid #e8edf2;
            vertical-align: top;
        }
        .descarga-cell:last-child { border-right: none; }
        .descarga-row:last-child .descarga-cell { border-bottom: none; }
        .descarga-cell .d-label {
            font-size: 9px; text-transform: uppercase; color: #888;
            font-weight: bold; margin-bottom: 4px; letter-spacing: .5px;
        }
        .descarga-cell .d-value {
            font-size: 13px; font-weight: bold; color: #0f172a;
        }
        .descarga-cell .d-value.empty {
            color: #adb5bd; font-style: italic; font-weight: normal; font-size: 11px;
        }
        .descarga-cell .d-sub {
            font-size: 9px; color: #888; margin-top: 3px;
        }

        /* ── Assinaturas ── */
        .sign-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .sign-cell {
            display: table-cell;
            width: 33.33%;
            padding-top: 8px;
            padding-right: 20px;
            text-align: center;
            border-top: 1px solid #c60a1a;
            vertical-align: top;
        }
        .sign-cell:last-child { padding-right: 0; }
        .sign-label {
            font-size: 9px; text-transform: uppercase; color: #888;
            font-weight: bold; letter-spacing: .5px;
        }

        /* ── Rodapé ── */
        .page-footer { border-top: 2px solid #c60a1a; padding-top: 10px;
                       display: flex; justify-content: space-between; align-items: flex-start; }
        .footer-user-label { font-size: 8px; text-transform: uppercase; letter-spacing: .5px;
                             color: #64748b; margin-bottom: 2px; }
        .footer-right { text-align: right; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>

    {{-- CABEÇALHO --}}
    <div class="header">
        <div class="company-block">
            <img src="{{ public_path('images/bymozelogo.png') }}" class="company-logo" alt="Logo">
            <div>
                <div class="company-name">Fábrica de Explosivos de Moçambique</div>
                <div class="company-detail">
                    Contribuinte Nº 400019029<br>
                    Av. Samora Machel Nº — Parcela 10<br>
                    Telef. +258 21 745 86/03 &nbsp;|&nbsp; FAX. +258 21 745 802
                </div>
            </div>
        </div>
        <div class="doc-title">
            <h1>REQUISIÇÃO DE MATERIAL</h1>
            <div class="doc-num">#{{ str_pad($requisicao->id, 4, '0', STR_PAD_LEFT) }}</div>
            <div class="doc-date">{{ $requisicao->date->format('d/m/Y') }}</div>
            <div>
                <span class="status-badge status-{{ $requisicao->status }}">
                    {{ $requisicao->status }}
                </span>
            </div>
            @if($requisicao->numero_guia)
                <div style="margin-top:6px;">
                    <span class="guia-badge">GUIA: {{ $requisicao->numero_guia }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- INFO CARDS --}}
    <div class="info-box">
        @if($requisicao->supplier)
        <div class="info-card" style="flex:2;">
            <div class="label">Fornecedor</div>
            <div class="value">{{ $requisicao->supplier->name }}</div>
            @if($requisicao->supplier->nuit)
                <div class="sub">NUIT: {{ $requisicao->supplier->nuit }}</div>
            @endif
            @if($requisicao->supplier->address ?? null)
                <div class="sub">{{ $requisicao->supplier->address }}</div>
            @endif
        </div>
        @endif

        <div class="info-card" style="flex:2;">
            <div class="label">Destino / Local de Entrega</div>
            <div class="value">{{ $requisicao->destino }}</div>
        </div>

        <div class="info-card" style="flex:1.5;">
            <div class="label">Transporte</div>
            <div class="value">{{ $requisicao->motorista ?? '—' }}</div>
            @if($requisicao->matricula)
                <div class="sub">Matrícula: {{ $requisicao->matricula }}</div>
            @endif
        </div>

        <div class="info-card" style="flex:1.5;">
            <div class="label">Responsável</div>
            <div class="value">{{ $requisicao->responsavel ?? '—' }}</div>
            <div class="sub">Emitido: {{ $requisicao->created_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    {{-- TABELA DE ITENS --}}
    <table>
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:60%">Descrição</th>
                <th class="right" style="width:20%">Quantidade</th>
                <th class="center" style="width:15%">Unid.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requisicao->items as $i => $item)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td class="right">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                <td class="center">{{ $item->unit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- OBSERVAÇÕES --}}
    @if($requisicao->observacoes)
    <div class="obs-box">
        <div class="obs-label">Observações</div>
        {{ $requisicao->observacoes }}
    </div>
    @endif

    {{-- SECÇÃO DE DESCARGA — grid 2×2 --}}
    <div class="descarga-section">
        <div class="descarga-header">
            <span>Dados de Confirmação e Descarga</span>
            @if($requisicao->status === 'FINALIZADA')
                <span style="font-size:9px;background:#16a34a;padding:2px 10px;border-radius:20px;font-weight:bold;
                             -webkit-print-color-adjust:exact;print-color-adjust:exact;">
                    ✓ FINALIZADA
                </span>
            @else
                <span style="font-size:9px;color:rgba(255,255,255,.4);">Pendente de confirmação</span>
            @endif
        </div>

        <div class="descarga-grid">
            {{-- Linha 1 --}}
            <div class="descarga-row">
                <div class="descarga-cell">
                    <div class="d-label">Nº Guia de Remessa</div>
                    @if($requisicao->numero_guia)
                        <div class="d-value">{{ $requisicao->numero_guia }}</div>
                    @else
                        <div class="d-value empty">Não registado</div>
                    @endif
                </div>
                <div class="descarga-cell">
                    <div class="d-label">Local de Descarga</div>
                    @if($requisicao->local_descarga)
                        <div class="d-value">{{ $requisicao->local_descarga }}</div>
                    @else
                        <div class="d-value empty">Não registado</div>
                    @endif
                </div>
            </div>
            {{-- Linha 2 --}}
            <div class="descarga-row">
                <div class="descarga-cell">
                    <div class="d-label">Peso Confirmado</div>
                    @if($requisicao->peso_confirmado)
                        <div class="d-value">{{ number_format($requisicao->peso_confirmado, 3, ',', '.') }}</div>
                        <div class="d-sub">kg</div>
                    @else
                        <div class="d-value empty">—</div>
                    @endif
                </div>
                <div class="descarga-cell">
                    <div class="d-label">Valor da Carga</div>
                    @if($requisicao->valor_carga)
                        <div class="d-value">{{ number_format($requisicao->valor_carga, 2, ',', '.') }}</div>
                        <div class="d-sub">MT</div>
                    @else
                        <div class="d-value empty">—</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ASSINATURAS --}}
    <div class="sign-section">
        <div class="sign-cell">
            <div class="sign-label">Emitido por / Responsável de Expedição</div>
        </div>
        <div class="sign-cell">
            <div class="sign-label">Motorista / Transportador</div>
        </div>
        <div class="sign-cell">
            <div class="sign-label">Recebido por / Responsável de Descarga</div>
        </div>
    </div>

    {{-- RODAPÉ --}}
    <div class="page-footer">
        <div>
            <div class="footer-user-label">Documento emitido por</div>
            <div style="font-size:11px;font-weight:bold;">{{ $requisicao->creator->name ?? auth()->user()->name }}</div>
            <div style="font-size:10px;color:#555;">{{ $requisicao->creator->email ?? auth()->user()->email }}</div>
        </div>
        <div class="footer-right">
            Documento gerado automaticamente pelo sistema FEM.<br>
            Não requer assinatura electrónica.
        </div>
    </div>

</body>
</html>