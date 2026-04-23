<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Requisição de Material #{{ str_pad($requisicao->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; padding: 30px; }

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

        .info-box  { display: flex; gap: 20px; margin-bottom: 20px; }
        .info-card { flex: 1; background: #fdf2f2; border-left: 3px solid #c60a1a;
                     padding: 10px 14px; border-radius: 3px;
                     -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .info-card .label { font-size: 9px; text-transform: uppercase; color: #888;
                            font-weight: bold; margin-bottom: 4px; }
        .info-card .value { font-size: 12px; font-weight: bold; color: #222; }
        .info-card .sub   { font-size: 10px; color: #555; margin-top: 2px; }

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

        .totals { width: 280px; margin-left: auto; margin-bottom: 24px; }
        .totals table { margin-bottom: 0; }
        .totals tr.total-final { background: #c60a1a; color: white;
                   -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .totals tr.total-final td { padding: 8px 10px; font-size: 13px; font-weight: bold; }
        .totals td.right { text-align: right; }

        .obs-box { background: #f8f9fa; border-left: 3px solid #c60a1a;
                   padding: 10px 14px; border-radius: 3px; margin-bottom: 24px; font-size: 11px; }
        .obs-box .obs-label { font-size: 9px; text-transform: uppercase; color: #888;
                              font-weight: bold; margin-bottom: 4px; }

        .page-footer { margin-top: 32px; border-top: 2px solid #c60a1a; padding-top: 14px; }
        .footer-user-label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px;
                             color: #64748b; margin-bottom: 2px; }
    </style>
</head>
<body>

    {{-- CABEÇALHO --}}
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
<th style="width:50%">Descrição</th>
<th class="right" style="width:20%">Quantidade</th>
<th class="center" style="width:25%">Unid.</th>
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

    

    {{-- OBSERVAÇÕES --}}
    @if($requisicao->observacoes)
    <div class="obs-box">
        <div class="obs-label">Observações</div>
        {{ $requisicao->observacoes }}
    </div>
    @endif

    {{-- RODAPÉ --}}
    <div class="page-footer">
        <div class="footer-user-label">Emitido por</div>
        <div style="font-size:11px;font-weight:bold;">{{ $requisicao->creator->name ?? auth()->user()->name }}</div>
        <div style="font-size:10px;color:#555;">{{ $requisicao->creator->email ?? auth()->user()->email }}</div>
    </div>

</body>
</html>