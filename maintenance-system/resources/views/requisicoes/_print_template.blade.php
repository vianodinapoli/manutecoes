<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisição #{{ $requisicao->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; padding: 30px; }

        /* ── Cabeçalho ── */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 3px solid #c60a1a; padding-bottom: 16px; }

        .company-block { display: flex; align-items: flex-start; gap: 14px; }
        .company-logo { width: 72px; height: auto; }
        .company-info { display: flex; flex-direction: column; justify-content: center; }
        .company-name { font-size: 13px; font-weight: bold; color: #c60a1a; line-height: 1.3; }
        .company-detail { font-size: 9px; color: #555; margin-top: 3px; line-height: 1.6; }

        .doc-title { text-align: right; }
        .doc-title h1 { font-size: 18px; font-weight: bold; color: #c60a1a; }
        .doc-title .doc-num { font-size: 13px; color: #444; margin-top: 4px; }
        .doc-title .doc-date { font-size: 11px; color: #888; margin-top: 2px; }

        /* ── Info cards ── */
        .info-box { display: flex; gap: 20px; margin-bottom: 20px; }
        .info-card { flex: 1; background: #fdf2f2; border-left: 3px solid #c60a1a; padding: 10px 14px; border-radius: 3px; }
        .info-card .label { font-size: 9px; text-transform: uppercase; color: #888; font-weight: bold; margin-bottom: 4px; }
        .info-card .value { font-size: 12px; font-weight: bold; color: #222; }
        .info-card .sub { font-size: 10px; color: #555; margin-top: 2px; }

        /* ── Tabela ── */
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        thead tr { background: #c60a1a; color: white; }
        thead th { padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; }
        thead th.right  { text-align: right; }
        thead th.center { text-align: center; }
        tbody tr { border-bottom: 1px solid #e8edf2; }
        tbody tr:nth-child(even) { background: #fdf5f5; }
        tbody td { padding: 8px 10px; font-size: 11px; }
        tbody td.right  { text-align: right; }
        tbody td.center { text-align: center; }

        /* ── Desconto na linha ── */
        .disc-val { color: #d97706; font-size: 9px; font-weight: bold; margin-top: 2px; }

        /* ── Totais ── */
        .totals { width: 300px; margin-left: auto; margin-bottom: 24px; }
        .totals table { margin-bottom: 0; }
        .totals td { padding: 5px 10px; font-size: 11px; }
        .totals td.label { color: #666; }
        .totals td.value { text-align: right; font-weight: bold; }
        .totals tr.disc-row td { color: #d97706; }
        .totals tr.iva-row td  { color: #c60a1a; }
        .totals tr.sep td { border-top: 1px solid #dee2e6; padding-top: 8px; }
        .totals tr.total-final { background: #c60a1a; color: white; }
        .totals tr.total-final td { padding: 8px 10px; font-size: 13px; font-weight: bold; color: white; }

        /* ── Rodapé ── */
        .page-footer {
            margin-top: 32px;
            border-top: 2px solid #c60a1a;
            padding-top: 14px;
            display: table;
            width: 100%;
        }
        .footer-user-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 2px;
        }

        /* ── Status badge ── */
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: bold; }
        .status-PENDENTE  { background: #fff3cd; color: #856404; }
        .status-APROVADO  { background: #d1e7dd; color: #0f5132; }
        .status-CANCELADO { background: #f8d7da; color: #c60a1a; }
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
            <h1>REQUISIÇÃO DE COMPRA</h1>
            <div class="doc-num">#{{ str_pad($requisicao->id, 4, '0', STR_PAD_LEFT) }}</div>
            <div class="doc-date">{{ $requisicao->date->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- INFO FORNECEDOR + ESTADO --}}
    <div class="info-box">
        <div class="info-card">
            <div class="label">Fornecedor</div>
            <div class="value">{{ $requisicao->supplier->name }}</div>
            @if($requisicao->supplier->nuit)
                <div class="sub">NUIT: {{ $requisicao->supplier->nuit }}</div>
            @endif
            @if($requisicao->supplier->address ?? $requisicao->supplier->endereco ?? null)
                <div class="sub">{{ $requisicao->supplier->address ?? $requisicao->supplier->endereco }}</div>
            @endif
        </div>
        <div class="info-card">
            <div class="sub">Criado em {{ $requisicao->created_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    {{-- TABELA DE ITENS --}}
    <table>
        <thead>
            <tr>
                <th style="width:4%">#</th>
                <th style="width:44%">Descrição</th>
                <th class="right" style="width:9%">Qtd</th>
                <th class="right" style="width:16%">Preço Unit.</th>
                <th class="center" style="width:9%">Desc %</th>
                <th class="right" style="width:18%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requisicao->items as $i => $item)
           @php
    $disc      = $item->discount ?? 0;               // percentagem de desconto
    $bruto     = $item->quantity * $item->unit_price;
    $desconto  = $bruto * ($disc / 100);
    $liquido   = $bruto - $desconto;
@endphp
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">{{ number_format($item->unit_price, 2, ',', '.') }} MT</td>
                <td class="center">
                    @if($disc > 0)
                        <strong style="color:#d97706;">{{ number_format($disc, 2, ',', '.') }}%</strong>
                    @else
                        —
                    @endif
                </td>
                <td class="right">
                    {{ number_format($liquido, 2, ',', '.') }} MT
                    @if($disc > 0)
                        <div class="disc-val">− {{ number_format($desconto, 2, ',', '.') }} MT</div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTAIS --}}
    @php
        $totalBruto    = $requisicao->items->sum(fn($it) => $it->quantity * $it->unit_price);
        $totalDesconto = $requisicao->discount_amount ?? ($totalBruto - $requisicao->total_liquid);
        $totalLiquido  = $requisicao->total_liquid  ?? ($totalBruto - $totalDesconto);
        $temDesconto   = $totalDesconto > 0.001;
        $temIva        = $requisicao->has_tax && $requisicao->tax_amount > 0;
        $totalIva      = $requisicao->tax_amount ?? 0;
        $totalFinal    = $requisicao->total_final;
    @endphp

    <div class="totals">
        <table>
            {{-- Subtotal Bruto --}}
            <tr>
                <td class="label">Subtotal Bruto</td>
                <td class="value">{{ number_format($totalBruto, 2, ',', '.') }} MT</td>
            </tr>

            {{-- Desconto comercial — só se houver --}}
            @if($temDesconto)
            <tr class="disc-row">
                <td class="label" style="color:#d97706;">Desconto Comercial</td>
                <td class="value" style="color:#d97706;">− {{ number_format($totalDesconto, 2, ',', '.') }} MT</td>
            </tr>
            @endif

            {{-- Subtotal Líquido --}}
            <tr>
                <td class="label">Subtotal Líquido</td>
                <td class="value">{{ number_format($totalLiquido, 2, ',', '.') }} MT</td>
            </tr>

            {{-- IVA — só se aplicado --}}
            @if($temIva)
            <tr class="iva-row">
                <td class="label" style="color:#c60a1a;">IVA (16%)</td>
                <td class="value" style="color:#c60a1a;">{{ number_format($totalIva, 2, ',', '.') }} MT</td>
            </tr>
            @endif

            {{-- Total Geral --}}
            <tr class="total-final">
                <td class="label">TOTAL GERAL</td>
                <td class="value">{{ number_format($totalFinal, 2, ',', '.') }} MT</td>
            </tr>
        </table>
    </div>

    {{-- RODAPÉ --}}
    <div class="page-footer">
        <div class="footer-left">
            <div class="footer-user-label">Emitido por</div>
            <div class="footer-user-name">{{ auth()->user()->name }}</div>
            <div class="footer-user-email">{{ auth()->user()->email }}</div>
        </div>
    </div>

</body>
</html>