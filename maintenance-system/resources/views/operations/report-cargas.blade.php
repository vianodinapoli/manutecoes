<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cargas por transportadora — {{ $operation->navio }}</title>

    @php
        $n     = fn ($v, $d = 0) => number_format((float) $v, $d, ',', '.');
        $q     = fn ($v) => rtrim(rtrim(number_format((float) $v, 3, ',', '.'), '0'), ',');
        $refDoc = 'CARGAS-' . str_pad($operation->id, 4, '0', STR_PAD_LEFT) . '/' . now()->format('Y');
        $maxCargas = max(1, (int) $porTransp->max('cargas'));
        $stLbl = ['confirmed' => 'Confirmado', 'in_transit' => 'Em trânsito', 'pending' => 'Pendente'];
        $extras = $operation->transportadorasExtra();
    @endphp

    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 12mm 14mm 12mm;
            @bottom-left  { content: "{{ $refDoc }} · {{ $operation->navio }}"; font-size: 8px; color: #888; }
            @bottom-right { content: "Página " counter(page) " de " counter(pages); font-size: 8px; color: #888; }
        }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        body { font-family: -apple-system, "Segoe UI", Helvetica, Arial, sans-serif; color: #1e293b; margin: 0;
               background: #eef1f5; font-size: 11px; line-height: 1.4; }

        .toolbar { position: sticky; top: 0; z-index: 10; background: #0e1015; color: #fff; padding: 10px 24px;
                   display: flex; justify-content: space-between; align-items: center; }
        .toolbar a, .toolbar button { font: inherit; font-size: 12px; color: #fff; text-decoration: none; cursor: pointer;
                   background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2); border-radius: 6px; padding: 6px 14px; }
        .toolbar button.primary { background: #c60a1a; border-color: #c60a1a; font-weight: 600; }

        .page { background: #fff; max-width: 1180px; margin: 18px auto; padding: 26px 30px; box-shadow: 0 2px 14px rgba(0,0,0,.08); }

        .doc-head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #c60a1a; padding-bottom: 12px; margin-bottom: 14px; }
        .doc-org { font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; color: #64748b; }
        .doc-title { font-size: 20px; font-weight: 800; margin: 2px 0 0; letter-spacing: -.3px; }
        .doc-sub { font-size: 12px; color: #475569; margin-top: 2px; }
        .doc-meta { text-align: right; font-size: 10px; color: #64748b; line-height: 1.6; }
        .doc-meta b { color: #1e293b; }

        h2 { font-size: 11px; letter-spacing: 1.2px; text-transform: uppercase; color: #fff; background: #1e293b;
             padding: 5px 10px; margin: 18px 0 8px; border-radius: 3px; page-break-after: avoid; }
        h2 span { color: #fca5a5; margin-right: 6px; }
        .sec { page-break-inside: avoid; }

        .crit { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
        .crit .chip { background: #e7f1ff; color: #0d6efd; border: 1px solid #b6d0ff; border-radius: 20px; padding: 2px 10px; font-size: 10px; font-weight: 600; }
        .crit .chip.none { background: #f1f5f9; color: #64748b; border-color: #e2e8f0; }
        .regra { margin-top: 8px; font-size: 10.5px; color: #475569; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 6px 10px; }

        .cards { display: grid; grid-template-columns: repeat(6, 1fr); gap: 8px; }
        .card { border: 1px solid #e2e8f0; border-left: 4px solid var(--c, #1a56db); border-radius: 4px; padding: 8px 10px; }
        .card .l { font-size: 8.5px; text-transform: uppercase; letter-spacing: .8px; color: #94a3b8; font-weight: 700; }
        .card .v { font-size: 19px; font-weight: 800; line-height: 1.15; margin-top: 2px; }
        .card .s { font-size: 9px; color: #64748b; margin-top: 1px; }

        table { width: 100%; border-collapse: collapse; }
        th { background: #f1f5f9; color: #475569; font-size: 8.5px; text-transform: uppercase; letter-spacing: .6px;
             padding: 5px 6px; border: 1px solid #e2e8f0; text-align: left; }
        td { padding: 5px 6px; border: 1px solid #e2e8f0; font-size: 10.5px; vertical-align: middle; }
        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
        .r { text-align: right; } .c { text-align: center; } .nw { white-space: nowrap; }
        tfoot td { background: #f8fafc; font-weight: 700; }
        .mut { color: #94a3b8; } .neg { color: #dc3545; font-weight: 700; } .pos { color: #198754; font-weight: 700; }
        .big { font-size: 13px; font-weight: 800; }
        .cn { color: #1a56db; } .cl { color: #dc3545; }

        .stack { display: flex; height: 10px; border-radius: 5px; overflow: hidden; background: #eef1f5; min-width: 120px; }
        .stack i { display: block; height: 100%; }
        .stack .sn { background: #1a56db; } .stack .sl { background: #dc3545; } .stack .sd { background: #cbd5e1; }

        .tag { display: inline-block; font-size: 8.5px; font-weight: 700; padding: 1px 7px; border-radius: 20px; }
        .tag.normal { background: #e8f0fe; color: #1a56db; }
        .tag.link   { background: #fde8e8; color: #dc3545; }
        .tag.nd     { background: #f1f5f9; color: #64748b; }
        .tag.extra  { background: #fd7e14; color: #fff; }
        .tag.warn   { background: #fff3cd; color: #856404; }
        tr.inc td { background: #fff7e6; }
        .prod { display: block; white-space: nowrap; }

        .nota { font-size: 9px; color: #94a3b8; margin-top: 8px; }
        .assin { display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; margin-top: 34px; page-break-inside: avoid; }
        .assin div { border-top: 1px solid #1e293b; padding-top: 4px; text-align: center; font-size: 10px; color: #475569; }

        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .page { box-shadow: none; margin: 0; padding: 0; max-width: none; }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <a href="{{ route('operations.show', $operation) }}">← Voltar à operação</a>
    <div>
        <span style="font-size:11px;opacity:.7;margin-right:12px;">Para guardar em PDF, escolha “Guardar como PDF” no destino da impressão</span>
        <button class="primary" onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>
</div>

<div class="page">

    {{-- CABEÇALHO --}}
    <div class="doc-head">
        <div>
            <div class="doc-org">SGL DEMO</div>
            <div class="doc-title">Cargas por transportadora e tipo de atrelado</div>
            <div class="doc-sub">Navio {{ $operation->navio }} · {{ $operation->empresa }}@if($operation->bl) · BL {{ $operation->bl }}@endif</div>
        </div>
        <div class="doc-meta">
            <div>Ref.: <b>{{ $refDoc }}</b></div>
            <div>Emitido em: <b>{{ now()->format('d/m/Y H:i') }}</b></div>
            <div>Emitido por: <b>{{ auth()->user()->name }}</b></div>
        </div>
    </div>

    {{-- 1. CRITÉRIOS --}}
    <div class="sec">
        <h2><span>1</span>Critérios deste relatório</h2>
        <div class="crit">
            @forelse($criterios as $c)
                <span class="chip">{{ $c }}</span>
            @empty
                <span class="chip none">Sem filtros: todas as cargas da operação</span>
            @endforelse
        </div>
        <div class="regra">
            <b>Regra do atrelado (definido no porto):</b>
            <b class="cn">Normal</b> = até {{ $limite }} sacos &nbsp;·&nbsp;
            <b class="cl">Link</b> = mais de {{ $limite }} sacos.
            Se o tipo escolhido não bate certo com o nº de sacos, a carga aparece assinalada a amarelo.
        </div>
    </div>

    {{-- 2. RESUMO --}}
    <div class="sec">
        <h2><span>2</span>Resumo</h2>
        <div class="cards">
            <div class="card" style="--c:#1e293b;"><div class="l">Total de cargas</div><div class="v">{{ $resumo['cargas'] }}</div><div class="s">{{ $resumo['transportadoras'] }} transportadora(s)</div></div>
            <div class="card" style="--c:#1a56db;"><div class="l">Atrelado normal</div><div class="v cn">{{ $resumo['normal'] }}</div><div class="s">até {{ $limite }} sacos</div></div>
            <div class="card" style="--c:#dc3545;"><div class="l">Atrelado link</div><div class="v cl">{{ $resumo['link'] }}</div><div class="s">mais de {{ $limite }} sacos</div></div>
            <div class="card" style="--c:#94a3b8;"><div class="l">Tipo não definido</div><div class="v">{{ $resumo['nd'] }}</div><div class="s">registos sem tipo</div></div>
            <div class="card" style="--c:#6f42c1;"><div class="l">Total de sacos</div><div class="v">{{ $n($resumo['sacos']) }}</div><div class="s">{{ $n($resumo['estimado_kg'] / 1000, 1) }} t estimadas</div></div>
            <div class="card" style="--c:{{ $resumo['inconsistentes'] ? '#fd7e14' : '#198754' }};"><div class="l">Tipo vs. sacos</div>
                <div class="v">{{ $resumo['inconsistentes'] }}</div><div class="s">{{ $resumo['inconsistentes'] ? 'cargas a rever' : 'tudo coerente' }}</div></div>
        </div>
    </div>

    {{-- 3. POR TRANSPORTADORA --}}
    <div class="sec">
        <h2><span>3</span>Por transportadora</h2>
        <table>
            <thead>
                <tr>
                    <th>Transportadora</th>
                    <th class="c">Cargas</th>
                    <th class="c">Normal</th>
                    <th class="c">Link</th>
                    <th class="c">Sem tipo</th>
                    <th style="width:140px;">Normal / Link</th>
                    <th class="c">% link</th>
                    <th class="r">Sacos</th>
                    <th class="r">Média sacos/carga</th>
                    <th class="r">Peso estimado (kg)</th>
                    <th class="r">Peso líquido (kg)</th>
                    <th class="c">A rever</th>
                </tr>
            </thead>
            <tbody>
                @forelse($porTransp as $t)
                <tr>
                    <td><b>{{ $t['nome'] }}</b>@if($t['extra']) <span class="tag extra">⚠ fora da lista</span>@endif</td>
                    <td class="c big">{{ $t['cargas'] }}</td>
                    <td class="c big cn">{{ $t['normal'] }}</td>
                    <td class="c big cl">{{ $t['link'] }}</td>
                    <td class="c {{ $t['nd'] ? '' : 'mut' }}">{{ $t['nd'] }}</td>
                    <td>
                        <div class="stack" title="{{ $t['normal'] }} normal · {{ $t['link'] }} link · {{ $t['nd'] }} sem tipo">
                            <i class="sn" style="width:{{ $t['cargas'] ? $t['normal'] / $maxCargas * 100 : 0 }}%"></i>
                            <i class="sl" style="width:{{ $t['cargas'] ? $t['link']   / $maxCargas * 100 : 0 }}%"></i>
                            <i class="sd" style="width:{{ $t['cargas'] ? $t['nd']     / $maxCargas * 100 : 0 }}%"></i>
                        </div>
                    </td>
                    <td class="c">{{ $t['pct_link'] }}%</td>
                    <td class="r">{{ $n($t['sacos']) }}</td>
                    <td class="r">{{ $n($t['media_sacos'], 1) }}</td>
                    <td class="r">{{ $n($t['estimado_kg']) }}</td>
                    <td class="r">{{ $n($t['liquido_kg']) }}</td>
                    <td class="c {{ $t['inconsistentes'] ? 'neg' : 'mut' }}">{{ $t['inconsistentes'] }}</td>
                </tr>
                @empty
                <tr><td colspan="12" class="c mut">Nenhuma carga corresponde aos filtros escolhidos.</td></tr>
                @endforelse
            </tbody>
            @if($porTransp->isNotEmpty())
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td class="c big">{{ $resumo['cargas'] }}</td>
                    <td class="c big cn">{{ $resumo['normal'] }}</td>
                    <td class="c big cl">{{ $resumo['link'] }}</td>
                    <td class="c">{{ $resumo['nd'] }}</td>
                    <td></td>
                    <td class="c">{{ $resumo['cargas'] ? round($resumo['link'] / $resumo['cargas'] * 100) : 0 }}%</td>
                    <td class="r">{{ $n($resumo['sacos']) }}</td>
                    <td class="r">{{ $resumo['cargas'] ? $n($resumo['sacos'] / $resumo['cargas'], 1) : 0 }}</td>
                    <td class="r">{{ $n($resumo['estimado_kg']) }}</td>
                    <td class="r">{{ $n($resumo['liquido_kg']) }}</td>
                    <td class="c">{{ $resumo['inconsistentes'] }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
        <div class="nota">A barra mostra normal (azul), link (vermelho) e sem tipo (cinza) em relação à transportadora com mais cargas. “Peso líquido” soma só as cargas já confirmadas na balança.</div>
    </div>

    {{-- 4. DETALHE --}}
    <div>
        <h2><span>4</span>Detalhe das cargas ({{ $discharges->count() }})</h2>
        <table>
            <thead>
                <tr>
                    <th>Nº / Data</th><th>Guia</th><th>Transportadora</th><th>Motorista</th><th>Matrícula</th>
                    <th class="c">Atrelado</th><th class="r">Sacos</th><th>Produtos</th>
                    <th class="r">Peso est. (kg)</th><th class="r">Líquido (kg)</th><th class="c">Estado</th><th>Alerta</th>
                </tr>
            </thead>
            <tbody>
                @forelse($discharges as $d)
                @php $inc = $d->atreladoInconsistente(); @endphp
                <tr class="{{ $inc ? 'inc' : '' }}">
                    <td class="nw"><b>#{{ $d->id }}</b> · {{ $d->data->format('d/m/Y') }}</td>
                    <td class="nw"><b>{{ $d->numero_guia }}</b></td>
                    <td>{{ $d->transportadora }}@if(in_array(mb_strtolower($d->transportadora), $extras, true)) <span class="tag extra">⚠</span>@endif</td>
                    <td>{{ $d->motorista }}</td>
                    <td class="nw">{{ $d->matricula }}</td>
                    <td class="c"><span class="tag {{ $d->tipo_atrelado ?: 'nd' }}">{{ $d->tipo_atrelado ? strtoupper($d->tipo_atrelado) : 'SEM TIPO' }}</span></td>
                    <td class="r"><b>{{ $d->total_sacos }}</b></td>
                    <td>
                        @foreach($d->items as $l)
                            <span class="prod">{{ $l->operationItem->nome }}: {{ $q($l->quantidade) }}</span>
                        @endforeach
                    </td>
                    <td class="r">{{ $n($d->peso_total_estimado) }}</td>
                    <td class="r">{{ $d->peso_liquido !== null ? $n($d->peso_liquido) : '—' }}</td>
                    <td class="c">{{ $stLbl[$d->status] ?? $d->status }}</td>
                    <td>
                        @if($inc)
                            <span class="tag warn">{{ $d->tipo_atrelado === 'normal' ? 'Normal com mais de ' . $limite . ' sacos' : 'Link com ' . $limite . ' sacos ou menos' }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="12" class="c mut">Sem cargas para os filtros escolhidos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="assin">
        <div>Elaborado por<br><b>{{ auth()->user()->name }}</b></div>
        <div>Verificado por</div>
        <div>Aprovado por</div>
    </div>
    <div class="nota" style="text-align:center;">SGL DEMO · {{ $refDoc }} · {{ now()->format('d/m/Y H:i') }}</div>

</div>
</body>
</html>