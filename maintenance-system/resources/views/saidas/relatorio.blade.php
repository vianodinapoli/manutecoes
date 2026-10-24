<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Relatório {{ $saida->codigo }}</title>
    <style>
        *{box-sizing:border-box}
        body{font-family:Arial,Helvetica,sans-serif;color:#1e293b;margin:0;padding:24px;font-size:12px;background:#fff}
        .barra{display:flex;gap:8px;margin-bottom:18px}
        .btn{border:1px solid #1e293b;background:#fff;color:#1e293b;padding:6px 14px;border-radius:6px;font-size:12px;
             font-weight:700;text-decoration:none;cursor:pointer}
        .btn.pri{background:#1e293b;color:#fff}
        h1{font-size:18px;margin:0 0 2px}
        .sub{color:#64748b;margin-bottom:14px}
        .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:16px}
        .cx{border:1px solid #e2e8f0;border-radius:8px;padding:8px 10px}
        .cx .l{font-size:9px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#94a3b8}
        .cx .v{font-size:15px;font-weight:700}
        h2{font-size:11px;letter-spacing:.9px;text-transform:uppercase;color:#64748b;margin:18px 0 6px}
        table{width:100%;border-collapse:collapse}
        th{font-size:9px;letter-spacing:.7px;text-transform:uppercase;color:#64748b;text-align:left;
           border-bottom:2px solid #1e293b;padding:5px 6px}
        td{padding:5px 6px;border-bottom:1px solid #e2e8f0;vertical-align:top}
        .r{text-align:right}
        .mat{font-weight:700;letter-spacing:.5px}
        .ok{color:#146c43;font-weight:700}
        .pend{color:#7a5b00;font-weight:700}
        .rodape{margin-top:22px;color:#94a3b8;font-size:10px}
        @media print{.barra{display:none}body{padding:0}}
    </style>
</head>
<body>
@php
    $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 3, ',', '.'), '0'), ',');
    $regs = $camioes->count();
    $carr = $camioes->where('status', 'carregada')->count();
@endphp

<div class="barra">
    <button class="btn pri" onclick="window.print()">Imprimir / Guardar PDF</button>
    <a class="btn" href="{{ route('saidas.relatorio', [$saida, 'formato' => 'xlsx']) }}">Excel</a>
    <a class="btn" href="{{ route('saidas.show', $saida) }}">Voltar</a>
</div>

<h1>Relatório de saída {{ $saida->codigo }}</h1>
<div class="sub">
    {{ $saida->cliente }} · {{ $saida->data?->format('d/m/Y') }}
    · Origem: {{ $saida->operation->navio ?? '—' }}{{ $saida->operation ? ' · ' . $saida->operation->empresa : '' }}
    · {{ $saida->isFechada() ? 'Fechada' : 'Aberta' }}
</div>

<div class="grid">
    <div class="cx"><div class="l">Camiões registados</div><div class="v">{{ $regs }}</div></div>
    <div class="cx"><div class="l">Carregados</div><div class="v">{{ $carr }}</div></div>
    <div class="cx"><div class="l">Por carregar</div><div class="v">{{ $regs - $carr }}</div></div>
    <div class="cx"><div class="l">Emitido em</div><div class="v" style="font-size:12px;">{{ now()->format('d/m/Y H:i') }}</div></div>
</div>

<h2>Resumo por produto</h2>
<table>
    <thead><tr><th>Produto</th><th>Unidade</th><th class="r">Pedido</th><th class="r">Carregado</th></tr></thead>
    <tbody>
    @forelse($resumo as $x)
        <tr>
            <td>{{ $x['nome'] }}</td><td>{{ $x['label'] }}</td>
            <td class="r">{{ $fmt($x['pedido']) }}</td><td class="r"><b>{{ $fmt($x['carregado']) }}</b></td>
        </tr>
    @empty
        <tr><td colspan="4" style="color:#94a3b8;">Ainda sem carga registada.</td></tr>
    @endforelse
    </tbody>
</table>

<h2>Camiões</h2>
<table>
    <thead>
        <tr>
            <th>#</th><th>Matrícula</th><th>Atrelado</th><th>Empresa</th><th>Motorista</th><th>Outros dados</th>
            <th>Produto</th><th class="r">Pedido</th><th class="r">Carregado</th><th>Estado</th>
        </tr>
    </thead>
    <tbody>
    @forelse($camioes as $c)
        @php $it = $itens[$c->operation_item_id] ?? null; $car = $c->status === 'carregada'; @endphp
        <tr>
            <td>{{ $c->id }}</td>
            <td class="mat">{{ $c->matricula }}</td>
            <td>{{ $c->tipo_atrelado ? strtoupper($c->tipo_atrelado) : '—' }}</td>
            <td>{{ $c->empresa }}</td>
            <td>{{ $c->nome_completo }}</td>
            <td>{{ collect($c->metadata ?? [])->map(fn ($v, $k) => "$k: $v")->implode(' · ') }}</td>
            <td>{{ $it->nome ?? '—' }}</td>
            <td class="r">{{ $fmt($c->quantidade_pedida ?? $c->quantidade) }}</td>
            <td class="r"><b>{{ $car ? $fmt($c->quantidade) : '—' }}</b></td>
            <td>
                @if($car)
                    <span class="ok">Carregado</span><br>
                    <span style="color:#64748b;font-size:10px;">{{ $c->carregado_em?->format('d/m/Y H:i') }}{{ $c->carregadoPor ? ' · ' . $c->carregadoPor->name : '' }}</span>
                @else
                    <span class="pend">Por carregar</span>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="10" style="color:#94a3b8;">Sem camiões registados.</td></tr>
    @endforelse
    </tbody>
</table>

<div class="rodape">Emitido por {{ auth()->user()->name ?? '—' }} · FEM — Sistema de Gestão</div>
</body>
</html>