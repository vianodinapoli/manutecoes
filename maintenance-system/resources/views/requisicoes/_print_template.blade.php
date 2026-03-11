<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requisição #{{ $requisicao->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; padding: 30px; }

        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 2px solid #1a56db; padding-bottom: 16px; }
        .company-name { font-size: 20px; font-weight: bold; color: #4cb343; }
        .company-sub { font-size: 10px; color: #666; margin-top: 2px; }

        .doc-title { text-align: right; }
        .doc-title h1 { font-size: 18px; font-weight: bold; color: #4cb343; }
        .doc-title .doc-num { font-size: 13px; color: #444; margin-top: 4px; }
        .doc-title .doc-date { font-size: 11px; color: #888; margin-top: 2px; }

        .info-box { display: flex; gap: 20px; margin-bottom: 20px; }
        .info-card { flex: 1; background: #f4f7fb; border-left: 3px solid #4cb343; padding: 10px 14px; border-radius: 3px; }
        .info-card .label { font-size: 9px; text-transform: uppercase; color: #888; font-weight: bold; margin-bottom: 4px; }
        .info-card .value { font-size: 12px; font-weight: bold; color: #222; }
        .info-card .sub { font-size: 10px; color: #555; margin-top: 2px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        thead tr { background: #4cb343; color: white; }
        thead th { padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; }
        thead th.right { text-align: right; }
        tbody tr { border-bottom: 1px solid #e8edf2; }
        tbody tr:nth-child(even) { background: #f9fbfd; }
        tbody td { padding: 8px 10px; font-size: 11px; }
        tbody td.right { text-align: right; }
        tbody td.center { text-align: center; }

        .totals { width: 260px; margin-left: auto; margin-bottom: 24px; }
        .totals table { margin-bottom: 0; }
        .totals td { padding: 5px 10px; font-size: 11px; }
        .totals td.label { color: #666; }
        .totals td.value { text-align: right; font-weight: bold; }
        .totals tr.total-final { background: #4cb343; color: white; border-radius: 3px; }
        .totals tr.total-final td { padding: 8px 10px; font-size: 13px; }

        .footer { margin-top: 40px; border-top: 1px solid #ddd; padding-top: 16px; display: flex; justify-content: space-between; }
        .assinatura { text-align: center; width: 200px; }
        .assinatura .linha { border-top: 1px solid #333; margin-bottom: 6px; }
        .assinatura .nome { font-size: 10px; color: #555; }

        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: bold; }
        .status-PENDENTE { background: #fff3cd; color: #856404; }
        .status-APROVADO { background: #d1e7dd; color: #0f5132; }
        .status-CANCELADO { background: #f8d7da; color: #842029; }
    </style>
</head>
<body>

    {{-- CABEÇALHO --}}
    <div class="header">
        <div>
            <div class="company-name">{{ config('SG', 'BYMOZE') }}</div>
            <div class="company-sub">BYMOZE | SG</div>
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
            <div class="label">Estado</div>
            <div class="value">
                <span class="status-badge status-{{ $requisicao->status }}">
                    {{ $requisicao->status }}
                </span>
            </div>
            <div class="sub">Criado em {{ $requisicao->created_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    {{-- TABELA DE ITENS --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Descrição</th>
                <th class="right">Qtd</th>
                <th class="right">Preço Unit.</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requisicao->items as $i => $item)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">{{ number_format($item->unit_price, 2, ',', '.') }} MT</td>
                <td class="right">{{ number_format($item->subtotal, 2, ',', '.') }} MT</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTAIS --}}
    <div class="totals">
        <table>
            <tr>
                <td class="label">Total Líquido</td>
                <td class="value">{{ number_format($requisicao->total_liquid, 2, ',', '.') }} MT</td>
            </tr>
            @if($requisicao->has_tax)
            <tr>
                <td class="label">IVA (16%)</td>
                <td class="value">{{ number_format($requisicao->tax_amount, 2, ',', '.') }} MT</td>
            </tr>
            @endif
            <tr class="total-final">
                <td class="label" style="color: white;">TOTAL GERAL</td>
                <td class="value">{{ number_format($requisicao->total_final, 2, ',', '.') }} MT</td>
            </tr>
        </table>
    </div>

    {{-- RODAPÉ COM ASSINATURAS --}}
    <div class="footer">
        <div class="assinatura">
            <div class="linha"></div>
            <div class="nome">Solicitante</div>
        </div>
        {{-- <div class="assinatura">
            <div class="linha"></div>
            <div class="nome">Aprovado por</div>
        </div>
        <div class="assinatura">
            <div class="linha"></div>
            <div class="nome">Responsável Financeiro</div>
        </div> --}}
    </div>

</body>
</html>
