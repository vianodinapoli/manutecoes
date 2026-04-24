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
                  margin-bottom: 24px; border-bottom: 3px solid #c60a1a; padding-bottom: 16px; }
        .company-block { display: flex; align-items: flex-start; gap: 14px; }
        .company-logo  { width: 72px; height: auto; }
        .company-info  { display: flex; flex-direction: column; justify-content: center; }
        .company-name  { font-size: 13px; font-weight: bold; color: #c60a1a; line-height: 1.3; }
        .company-detail { font-size: 9px; color: #555; margin-top: 3px; line-height: 1.6; }
        .doc-title     { text-align: right; }
        .doc-title h1  { font-size: 18px; font-weight: bold; color: #c60a1a; }
        .doc-title .doc-num  { font-size: 13px; color: #444; margin-top: 4px; }
        .doc-title .doc-date { font-size: 11px; color: #888; margin-top: 2px; }

        /* ── Estado badge ── */
        .status-badge {
            display: inline-block; padding: 3px 10px; border-radius: 12px;
            font-size: 10px; font-weight: bold; letter-spacing: .4px; text-transform: uppercase;
            margin-top: 6px;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .status-emitida    { background: #dbeafe; color: #1d4ed8; }
        .status-confirmada { background: #fef9c3; color: #854d0e; }
        .status-finalizada { background: #dcfce7; color: #15803d; }
        .status-cancelado  { background: #fee2e2; color: #dc2626; }

        /* ── Info cards ── */
        .info-box  { display: flex; gap: 12px; margin-bottom: 20px; }
        .info-card { flex: 1; background: #fdf2f2; border-left: 3px solid #c60a1a;
                     padding: 10px 14px; border-radius: 3px;
                     -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .info-card .label { font-size: 9px; text-transform: uppercase; color: #888;
                            font-weight: bold; margin-bottom: 4px; }
        .info-card .value { font-size: 12px; font-weight: bold; color: #222; }
        .info-card .sub   { font-size: 10px; color: #555; margin-top: 2px; }

        /* ── Guia / Peso / Valor — destaque ── */
        .carga-info-box { display: flex; gap: 12px; margin-bottom: 20px; }
        .carga-card {
            flex: 1; padding: 10px 14px; border-radius: 4px;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .carga-card.guia   { background: #eff6ff; border-left: 3px solid #3b82f6; }
        .carga-card.peso   { background: #fefce8; border-left: 3px solid #eab308; }
        .carga-card.valor  { background: #f0fdf4; border-left: 3px solid #22c55e; }
        .carga-card .c-label { font-size: 8px; text-transform: uppercase; font-weight: bold;
                               color: #888; margin-bottom: 3px; letter-spacing: .7px; }
        .carga-card .c-value { font-size: 13px; font-weight: bold; color: #1e293b; }

        /* ── Tabela de itens ── */
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        thead tr { background: #c60a1a; color: white;
                   -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        thead th { padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; }
        thead th.right  { text-align: right; }
        thead th.center { text-align: center; }
        tbody tr { border-bottom: 1px solid #e8edf2; }
        tbody tr:nth-child(even) { background: #fdf5f5;
                   -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        tbody td { padding: 8px 10px; font-size: 11px; }
        tbody td.right  { text-align: right; }
        tbody td.center { text-align: center; }

        /* ── Observações ── */
        .obs-box { background: #f8f9fa; border-left: 3px solid #c60a1a;
                   padding: 10px 14px; border-radius: 3px; margin-bottom: 20px; font-size: 11px; }
        .obs-box .obs-label { font-size: 9px; text-transform: uppercase; color: #888;
                              font-weight: bold; margin-bottom: 4px; }

        /* ── Local de descarga ── */
        .descarga-box {
            background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b;
            border-radius: 4px; padding: 12px 16px; margin-bottom: 20px;
            -webkit-print-color-adjust: exact; print-color-adjust: exact;
        }
        .descarga-box .d-label {
            font-size: 9px; text-transform: uppercase; font-weight: bold;
            color: #92400e; letter-spacing: .7px; margin-bottom: 5px;
            display: flex; align-items: center; gap: 5px;
        }
        .descarga-box .d-value {
            font-size: 12px; font-weight: bold; color: #78350f;
        }
        .descarga-box .d-sub {
            font-size: 10px; color: #a16207; margin-top: 3px;
        }

        /* ── Rodapé ── */
        .page-footer { margin-top: 28px; border-top: 2px solid #c60a1a; padding-top: 14px;
                       display: flex; justify-content: space-between; align-items: flex-end; }
        .footer-user-label { font-size: 8px; text-transform: uppercase; letter-spacing: .5px;
                             color: #64748b; margin-bottom: 2px; }
        .footer-note { font-size: 9px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>

    {{-- ── CABEÇALHO ── --}}
    <div class="header">
        <div class="company-block">
            <img src="{{ public_path('images/bymozelogo.png') }}" class="company-logo" alt="Logo">
            <div class="company-info">
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
            @php
                $statusMap = [
                    'EMITIDA'    => ['status-emitida',    'Emitida'],
                    'CONFIRMADA' => ['status-confirmada', 'Confirmada'],
                    'FINALIZADA' => ['status-finalizada', 'Finalizada'],
                    'CANCELADO'  => ['status-cancelado',  'Cancelado'],
                ];
                [$statusCls, $statusLbl] = $statusMap[$requisicao->status] ?? ['status-emitida', $requisicao->status];
            @endphp
            <div>
                <span class="status-badge {{ $statusCls }}">{{ $statusLbl }}</span>
            </div>
        </div>
    </div>

    {{-- ── INFO CARDS ── --}}
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

    {{-- ── DADOS DA CARGA (Guia / Peso / Valor) — visível apenas quando preenchidos ── --}}
    @if($requisicao->numero_guia || $requisicao->peso_confirmado || $requisicao->valor_carga)
    <div class="carga-info-box">
        @if($requisicao->numero_guia)
        <div class="carga-card guia">
            <div class="c-label">&#x1F4CB; Nº Guia de Remessa</div>
            <div class="c-value">{{ $requisicao->numero_guia }}</div>
        </div>
        @endif
        @if($requisicao->peso_confirmado)
        <div class="carga-card peso">
            <div class="c-label">&#x2696; Peso Confirmado</div>
            <div class="c-value">{{ number_format($requisicao->peso_confirmado, 3, ',', '.') }} kg</div>
        </div>
        @endif
        @if($requisicao->valor_carga)
        <div class="carga-card valor">
            <div class="c-label">&#x1F4B0; Valor da Carga</div>
            <div class="c-value">{{ number_format($requisicao->valor_carga, 2, ',', '.') }} MT</div>
        </div>
        @endif
    </div>
    @endif

    {{-- ── TABELA DE ITENS ── --}}
    <table>
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:55%">Descrição</th>
                <th class="right" style="width:20%">Quantidade</th>
                <th class="center" style="width:20%">Unid.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requisicao->items as $i => $item)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td class="right">{{ number_format($item->quantity, 3, ',', '.') }}</td>
                <td class="center">{{ $item->unit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── OBSERVAÇÕES ── --}}
    @if($requisicao->observacoes)
    <div class="obs-box">
        <div class="obs-label">Observações</div>
        {{ $requisicao->observacoes }}
    </div>
    @endif

    {{-- ── LOCAL DE DESCARGA — secção em destaque na parte inferior ── --}}
    @if($requisicao->local_descarga)
    <div class="descarga-box">
        <div class="d-label">
            &#x1F4CD; Local de Descarga
        </div>
        <div class="d-value">{{ $requisicao->local_descarga }}</div>
        @if($requisicao->status === 'FINALIZADA')
            <div class="d-sub">
                Carga descarregada e requisição encerrada em
                {{ $requisicao->updated_at->format('d/m/Y \à\s H:i') }}.
            </div>
        @endif
    </div>
    @endif

    {{-- ── RODAPÉ ── --}}
    <div class="page-footer">
        <div>
            <div class="footer-user-label">Emitido por</div>
            <div style="font-size:11px;font-weight:bold;">{{ $requisicao->creator->name ?? auth()->user()->name }}</div>
            <div style="font-size:10px;color:#555;">{{ $requisicao->creator->email ?? auth()->user()->email }}</div>
        </div>
        <div class="footer-note">
            Documento gerado automaticamente.<br>
            Não requer assinatura.
        </div>
    </div>

</body>
</html>