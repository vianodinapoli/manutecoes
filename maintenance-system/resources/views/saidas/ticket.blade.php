<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket {{ $camiao->ticket_numero }}</title>
    <style>
        *{box-sizing:border-box}
        body{font-family:Arial,Helvetica,sans-serif;color:#1e293b;margin:0;padding:24px;font-size:12px;background:#f1f5f9}
        .barra{display:flex;gap:8px;margin:0 auto 14px;max-width:560px}
        .btn{border:1px solid #1e293b;background:#fff;color:#1e293b;padding:6px 14px;border-radius:6px;font-size:12px;
             font-weight:700;text-decoration:none;cursor:pointer}
        .btn.pri{background:#1e293b;color:#fff}
        .ticket{max-width:560px;margin:0 auto;background:#fff;border:1px solid #cbd5e1;border-radius:8px;padding:20px 24px}
        .topo{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:2px solid #1e293b;padding-bottom:10px;margin-bottom:12px}
        .topo h1{font-size:16px;margin:0}
        .topo .sub{color:#64748b;font-size:10px;margin-top:2px}
        .num{font-size:15px;font-weight:700;letter-spacing:.5px;text-align:right}
        .num small{display:block;font-size:10px;font-weight:400;color:#64748b;letter-spacing:0}
        h2{font-size:10px;letter-spacing:.9px;text-transform:uppercase;color:#64748b;margin:14px 0 5px}
        table{width:100%;border-collapse:collapse}
        td,th{padding:5px 6px;border-bottom:1px solid #e2e8f0;text-align:left;vertical-align:top}
        th{font-size:9px;letter-spacing:.7px;text-transform:uppercase;color:#64748b;border-bottom:2px solid #1e293b}
        .r{text-align:right}
        .l{color:#64748b;width:34%}
        .mat{font-weight:700;letter-spacing:.8px}
        .liq td{font-size:15px;font-weight:700;border-top:2px solid #1e293b;border-bottom:0}
        .aviso{margin-top:10px;border:1px solid #ffe08a;background:#fff8e1;color:#7a5b00;border-radius:6px;padding:6px 10px;font-weight:700}
        .assin{display:flex;gap:30px;margin-top:34px}
        .assin div{flex:1;border-top:1px solid #1e293b;padding-top:4px;text-align:center;font-size:10px;color:#64748b}
        .rodape{margin-top:14px;color:#94a3b8;font-size:10px;text-align:center}
        @media print{body{background:#fff;padding:0}.barra{display:none}.ticket{border:0;max-width:none}}
    </style>
</head>
<body>
@php
    $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 3, ',', '.'), '0'), ',');
@endphp

<div class="barra">
    <button class="btn pri" onclick="window.print()">Imprimir ticket</button>
    <a class="btn" href="{{ route('saidas.show', $saida) }}">Voltar</a>
</div>

<div class="ticket">
    <div class="topo">
        <div>
            <h1>Ticket de pesagem</h1>
            <div class="sub">FEM — Sistema de Gestão · Saída de carga</div>
        </div>
        <div class="num">{{ $camiao->ticket_numero }}<small>Emitido em {{ now()->format('d/m/Y H:i') }}</small></div>
    </div>

    <table>
        <tr><td class="l">Saída</td><td><b>{{ $saida->codigo }}</b> · {{ $saida->cliente }}</td></tr>
        <tr><td class="l">Origem</td><td>{{ $op->navio ?? '—' }}{{ $op ? ' · ' . $op->empresa : '' }}</td></tr>
        <tr><td class="l">Empresa</td><td>{{ $camiao->empresa }}</td></tr>
        <tr><td class="l">Motorista</td><td>{{ $camiao->nome_completo }}</td></tr>
        <tr><td class="l">Camião</td><td class="mat">{{ $camiao->matricula }}</td></tr>
        <tr>
            <td class="l">Atrelados{{ $camiao->tipo_atrelado ? ' (' . strtoupper($camiao->tipo_atrelado) . ')' : '' }}</td>
            <td class="mat">
                @forelse($atrelados as $a){{ $a->matricula }}@if(! $loop->last) · @endif @empty — @endforelse
            </td>
        </tr>
    </table>

    <h2>Pesagens</h2>
    <table>
        <thead><tr><th>Pesagem</th><th>Data / hora</th><th>Operador</th><th class="r">Peso (kg)</th></tr></thead>
        <tbody>
            <tr>
                <td>1ª · Vazio</td>
                <td>{{ $vazioEm?->format('d/m/Y H:i') ?? '—' }}</td>
                <td>{{ $users[$camiao->peso_vazio_por] ?? '—' }}</td>
                <td class="r"><b>{{ $camiao->peso_vazio !== null ? $fmt($camiao->peso_vazio) : '—' }}</b></td>
            </tr>
            <tr>
                <td>2ª · Cheio</td>
                <td>{{ $cheioEm?->format('d/m/Y H:i') ?? 'Por pesar' }}</td>
                <td>{{ $users[$camiao->peso_cheio_por] ?? '—' }}</td>
                <td class="r"><b>{{ $camiao->peso_cheio !== null ? $fmt($camiao->peso_cheio) : '—' }}</b></td>
            </tr>
        </tbody>
        @if($camiao->peso_liquido !== null)
        <tfoot><tr class="liq"><td colspan="3">Peso líquido</td><td class="r">{{ $fmt($camiao->peso_liquido) }} kg</td></tr></tfoot>
        @endif
    </table>

    @if($camiao->status === 'carregada')
        <h2>Carga</h2>
        <table>
            <tr><td class="l">Produto</td><td>{{ $item->nome ?? '—' }}</td></tr>
            <tr><td class="l">Quantidade</td><td><b>{{ $fmt($camiao->quantidade) }}</b> {{ $item->unidade_label ?? '' }}</td></tr>
            @if($camiao->observacoes)<tr><td class="l">Observações</td><td>{{ $camiao->observacoes }}</td></tr>@endif
        </table>
        @if($aviso)<div class="aviso">⚠ {{ $aviso }}</div>@endif
    @else
        <div class="rodape" style="margin-top:12px;">Ticket da 1ª pesagem · a carga ainda não foi pesada.</div>
    @endif

    <div class="assin">
        <div>Motorista</div>
        <div>Operador da balança</div>
    </div>

    <div class="rodape">Emitido por {{ auth()->user()->name ?? '—' }}</div>
</div>

@if(request('print'))
<script>window.addEventListener('load', () => window.print());</script>
@endif
</body>
</html>
